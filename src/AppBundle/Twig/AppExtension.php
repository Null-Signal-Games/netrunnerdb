<?php
namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('icon', [$this, 'icon'], ['is_safe' => ['html']]),
        ];
    }

    public function icon(string $name, string $className = '', string $title = ''): string
    {
        $class = 'icon icon-' . $name . ($className !== '' ? ' ' . $className : '');
        $titleAttr = $title !== '' ? ' title="' . htmlspecialchars($title, ENT_QUOTES) . '"' : '';
        return sprintf(
            '<svg class="%s"%s aria-hidden="true"><use xlink:href="/images/netrunner.svg#icon-%s"></use></svg>',
            $class,
            $titleAttr,
            $name
        );
    }
}
