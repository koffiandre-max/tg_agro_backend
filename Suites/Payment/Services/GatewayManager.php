<?php

namespace Modules\Payment\Services;

use InvalidArgumentException;
use Modules\Payment\Gateways\CinetPayGateway;
use Modules\Payment\Gateways\FedaPayGateway;
use Modules\Payment\Gateways\PaymentGateway;
use Modules\Payment\Gateways\SimulateGateway;

/**
 * Résout les passerelles de paiement déclarées dans config("payment.gateways").
 */
class GatewayManager
{
    /**
     * Instance du driver demandé (ou de la passerelle par défaut).
     *
     * Deux façons de déclarer une passerelle dans config("payment.gateways") :
     *  - 'driver' => 'simulate|fedapay|cinetpay' : drivers intégrés au module ;
     *  - 'driver_class' => \App\Services\MaPasserelle::class : classe fournie par
     *    l'application hôte (doit implémenter PaymentGateway) — permet d'ajouter
     *    une passerelle SANS toucher au code du module.
     */
    public function driver(?string $name = null): PaymentGateway
    {
        $name = $name ?: (string) config('payment.default_gateway', 'simulate');
        $config = config("payment.gateways.{$name}");

        if (! is_array($config)) {
            throw new InvalidArgumentException("Passerelle de paiement inconnue : {$name}.");
        }

        // Passerelle personnalisée fournie par l'app hôte (config-only).
        if (! empty($config['driver_class'])) {
            $class = $config['driver_class'];

            if (! class_exists($class) || ! is_subclass_of($class, PaymentGateway::class)) {
                throw new InvalidArgumentException(
                    "La classe {$class} doit exister et implémenter ".PaymentGateway::class.'.'
                );
            }

            return new $class();
        }

        return match ($config['driver'] ?? $name) {
            'simulate' => new SimulateGateway(),
            'fedapay' => new FedaPayGateway(),
            'cinetpay' => new CinetPayGateway(),
            default => throw new InvalidArgumentException(
                'Driver de paiement non supporté : '.($config['driver'] ?? $name).'.'
            ),
        };
    }

    /**
     * Liste des passerelles disponibles, pour l'affichage.
     *
     * @return array<string, string> name => label
     */
    public function available(): array
    {
        $list = [];

        foreach ((array) config('payment.gateways', []) as $name => $config) {
            $list[$name] = $config['label'] ?? $name;
        }

        return $list;
    }
}
