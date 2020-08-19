<?php

namespace App\Twig;

use App\Entity\Estatus;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EstatusExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
        //    new TwigFilter('draw_estatus', [$this, 'drawAsHtml',],['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('draw_estatus', [$this, 'drawAsHtml'],['is_safe' => ['html']]),
        ];
    }

    public function drawAsHtml($code,$text)
    {
        return self::drawAsHtmlStatic($code,$text);
    }

    public static function drawAsHtmlStatic($code,$text)
    {
        $class='info';
        switch ($code){
            case Estatus::ACTIVE_CODE:
                $class='success';
                break;
            case Estatus::DELETE_CODE:
                $class='danger';
                break;
            case Estatus::CANCEL_CODE:
                $class='warning';
                break;
        }
        return " <span class=\"badge badge-".$class." font-weight-bold text-uppercase\">".$text."</span>";
    }
}
