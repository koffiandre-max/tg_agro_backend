<?php

namespace Modules\SendEmail\Http\Controllers;

use App\Core\ModuleSDK;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\SendEmail\Http\Requests\SendEmailRequest;
use Modules\SendEmail\Models\EmailLog;
use Modules\SendEmail\Services\EmailSender;

/**
 * Envoi d'emails : composition, historique, relance des échecs.
 */
class SendEmailController extends BaseController
{
    public function __construct(protected EmailSender $sender)
    {
    }

    /**
     * Historique des emails + formulaire de composition.
     */
    public function index(Request $request)
    {
        $businessId = $this->getBusinessId();
        $search = trim((string) $request->query('q'));

        $emails = EmailLog::query()
            ->forBusiness($businessId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('to_email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('send_email::send_email.index', [
            'emails' => $emails,
            'search' => $search,
            'stats' => $this->sender->stats($businessId),
        ]);
    }

    /**
     * Envoie l'email composé depuis le formulaire.
     */
    public function store(SendEmailRequest $request)
    {
        $user = ModuleSDK::user();

        $log = $this->sender->send(
            businessId: $this->getBusinessId(),
            userId: $user?->id,
            toEmail: $request->validated('to_email'),
            data: $request->validated(),
        );

        if ($log->status === EmailLog::STATUS_SENT) {
            return redirect()
                ->route('admin.send_email.index')
                ->with('success', "Email envoyé à {$log->to_email}.");
        }

        return redirect()
            ->route('admin.send_email.show', $log)
            ->withErrors(['send' => 'L\'envoi a échoué : '.($log->error ?: 'erreur inconnue')]);
    }

    /**
     * Détail d'un email envoyé (avec erreur éventuelle).
     */
    public function show(EmailLog $emailLog)
    {
        abort_unless((int) $emailLog->business_id === (int) $this->getBusinessId(), 403);

        return view('send_email::send_email.show', ['email' => $emailLog]);
    }

    /**
     * Relance l'envoi d'un email (échec précédent).
     */
    public function resend(EmailLog $emailLog)
    {
        abort_unless((int) $emailLog->business_id === (int) $this->getBusinessId(), 403);

        $log = $this->sender->resend($emailLog);

        if ($log->status === EmailLog::STATUS_SENT) {
            return redirect()
                ->route('admin.send_email.show', $log)
                ->with('success', 'Email renvoyé avec succès.');
        }

        return back()->withErrors(['send' => 'La relance a échoué : '.($log->error ?: 'erreur inconnue')]);
    }
}
