<?php

namespace Simplex\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class TwigRenderer
{

    const NAMESPACE = '__main__';

    /**
     * Twig environment
     *
     * @var Environment
     */
    private $twig;

    /**
     * Template loader
     *
     * @var FilesystemLoader
     */
    private $loader;

    /**
     * Constructor
     *
     * @param Environment $twig
     * @param FilesystemLoader $loader
     */
    public function __construct(Environment $twig, FilesystemLoader $loader)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

    /**
     * Add a template path
     *
     * @param string $path
     * @param [type] $namespace
     * @return void
     */
    public function addPath(string $path, $namespace = self::NAMESPACE)
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Render a template
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function render(string $file, array $params = []): string
    {
        return $this->twig->render("$file.twig", $params);
    }
}