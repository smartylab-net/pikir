<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler as BaseAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class with the default authentication success handling logic.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class DefaultAuthenticationFailureHandler extends BaseAuthenticationFailureHandler
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param HttpKernelInterface $httpKernel
     * @param HttpUtils $httpUtils
     * @param array $options Options for processing a failed authentication attempt.
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param LoggerInterface $logger Optional logger
     */
    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, array $options, TranslatorInterface $translator, LoggerInterface $logger = null)
    {
        parent::__construct($httpKernel, $httpUtils, $options, $logger);
        $this->translator = $translator;
    }
    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('success' => false, 'error' => $this->translator->trans($exception->getMessage(), [], 'FOSUserBundle')), 401);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}
