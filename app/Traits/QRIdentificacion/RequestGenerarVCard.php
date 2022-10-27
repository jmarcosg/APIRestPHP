<?php

namespace App\Traits\QRIdentificacion;

use JeroenDesloovere\VCard\VCard;
use JeroenDesloovere\VCard\Property\Name;
use JeroenDesloovere\VCard\Property\Email;
use JeroenDesloovere\VCard\Property\Telephone;
use JeroenDesloovere\VCard\Property\Role;
use JeroenDesloovere\VCard\Formatter\Formatter;
use JeroenDesloovere\VCard\Formatter\VcfFormatter;
use JeroenDesloovere\VCard\Parser\Property\TelephoneParser;
use JeroenDesloovere\VCard\Property\Address;
use JeroenDesloovere\VCard\Property\Parameter\Type;
use JeroenDesloovere\VCard\Property\Parameter\Version;

class RequestGenerarVCard
{

    public static function generateVcard($params)
    {
        if (ENV == "local") {
            $path = "C:/laragon/www/APIRestPHP/files/qr-identificacion/";
        } else {
            $path = PROD ? "E:/Dataserver/Produccion/projects_files/qr-identificacion/" : "E:/Dataserver/Replica/projects_files/qr-identificacion/";
        }

        $path .= "$params[id]/";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        };

        $vcard = new VCard(null, new Version("3.0"));
        $vcard->add(new Name($params['apellido'], $params['nombre']));
        $vcard->add(new Email($params['email']));
        $vcard->add(new Telephone($params['telefono'], new Type("work;PREF=1")));
        $vcard->add(new Role($params['cargo']));

        if (!empty($params['telefono_alternativo']))
            $vcard->add(new Telephone($params['telefono_alternativo']), new Type("work"));

        if (!empty($params['lugar_trabajo']))
            $vcard->add(new Address(null, $params['lugar_trabajo'], null, "Neuquén", null, "8300", "Argentina", new Type("work")));

        $formatter = new Formatter(new VcfFormatter(), "tarjeta-de-contacto");
        $formatter->addVCard($vcard);
        $formatter->save($path);

        return true;
    }
}
