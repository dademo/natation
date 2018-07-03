<?php
/**
 * Created by PhpStorm.
 * User: jerem
 * Date: 03/07/2018
 * Time: 09:22
 */


namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Debug\Exception\FlattenException;

use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use vendor\symfony\symfony\src\Symfony\Bundle\TwigBundle\Resources\views\Exception;

class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController
{
    protected $twig;
    protected $debug;

    public function __construct(\Twig_Environment $twig, $debug)
    {
        $this->twig = $twig;
        $this->debug = $debug;
    }

    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $statusCode = $exception->getStatusCode();
        $status_text = ($statusCode == 404)? 'La page n\'existe pas' : 'Une erreur est survenue';
        return new Response($this->twig->render('@Natation/Exception/error.html.twig', array('status_code' => $statusCode, 'status_text' => $status_text)));
    }

}
