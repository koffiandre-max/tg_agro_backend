<?php

namespace App\Services;

use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class SendmailService
{
    public function send(string $to, string $subject, string $content, ?string $from = null): bool
    {
        try {
            Mail::raw($content, function (Message $message) use ($to, $subject, $from) {
                $message->to($to)->subject($subject);

                if ($from) {
                    $message->from($from);
                }
            });

            return true;
        } catch (\Throwable $e) {
            logger()->error('SendmailService error', [
                'message' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject,
            ]);

            return false;
        }
    }

    public function sendView(string $to, string $subject, string $view, array $data = [], ?string $from = null): bool
    {
        try {
            Mail::send($view, $data, function (Message $message) use ($to, $subject, $from) {
                $message->to($to)->subject($subject);

                if ($from) {
                    $message->from($from);
                }
            });

            return true;
        } catch (\Throwable $e) {
            logger()->error('SendmailService view error', [
                'message' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject,
                'view' => $view,
            ]);

            return false;
        }
    }

    public function sendMarkdown(string $to, string $subject, string $markdown, array $data = [], ?string $from = null): bool
    {
        try {
            Mail::markdown($markdown, $data, function (Message $message) use ($to, $subject, $from) {
                $message->to($to)->subject($subject);

                if ($from) {
                    $message->from($from);
                }
            });

            return true;
        } catch (\Throwable $e) {
            logger()->error('SendmailService markdown error', [
                'message' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject,
                'markdown' => $markdown,
            ]);

            return false;
        }
    }

    // public function queue(string $to, string $subject, string $content, ?string $from = null): bool
    // {
    //     try {
    //         Mail::to($to)->queue(new SendQueuedMail($subject, $content, function (Message $message) use ($to, $subject, $from) {
    //             $message->to($to)->subject($subject);

    //             if ($from) {
    //                 $message->from($from);
    //             }
    //         }));

    //         return true;
    //     } catch (\Throwable $e) {
    //         logger()->error('SendmailService queue error', [
    //             'message' => $e->getMessage(),
    //             'to' => $to,
    //             'subject' => $subject,
    //         ]);

    //         return false;
    //     }
    // }

    // public function later(\DateTimeInterface $delay, string $to, string $subject, string $content, ?string $from = null): bool
    // {
    //     try {
    //         Mail::to($to)->later($delay, new SendQueuedMailable($subject, $content, function (Message $message) use ($to, $subject, $from) {
    //             $message->to($to)->subject($subject);

    //             if ($from) {
    //                 $message->from($from);
    //             }
    //         }));

    //         return true;
    //     } catch (\Throwable $e) {
    //         logger()->error('SendmailService later error', [
    //             'message' => $e->getMessage(),
    //             'to' => $to,
    //             'subject' => $subject,
    //         ]);

    //         return false;
    //     }
    // }
}
