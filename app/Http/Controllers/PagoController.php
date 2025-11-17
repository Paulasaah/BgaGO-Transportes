<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Resources\Preference\PreferenceItemRequest;

class PagoController extends Controller
{
    public function crearPreferencia()
    {
        // Configurar token desde services.php
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        // Crear cliente
        $client = new PreferenceClient();

        // Crear item
        $item = new PreferenceItemRequest();
        $item->title = "Pago en BgaGO Transportes";
        $item->quantity = 1;
        $item->unit_price = 5000;

        // Crear preferencia MP
        $preference = $client->create([
            "items" => [$item],
            "back_urls" => [
                "success" => url("/pago-exitoso"),
                "failure" => url("/pago-fallido"),
                "pending" => url("/pago-pendiente"),
            ],
            "auto_return" => "approved",
        ]);

        return response()->json([
            "id" => $preference->id
        ]);
    }
}
