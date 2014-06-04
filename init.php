<?php
    if (isset($_SERVER['PHP_SELF'])) {
        Twig::set_global('PHP_SELF', $_SERVER['PHP_SELF']);
    }
    if (isset($_SERVER['HTTP_HOST'])) {
        Twig::set_global('HTTP_HOST', $_SERVER['HTTP_HOST']);
    }
    Meerkat\Event\Event::dispatcher()
        ->connect('MEERKAT_TWIG_ENVIRONMENT', function (\sfEvent $event, $parameters = null) {
            Meerkat\Twig\Twig::environment()
                ->addExtension(new \Meerkat\Twig\Twig_Extension());

        });