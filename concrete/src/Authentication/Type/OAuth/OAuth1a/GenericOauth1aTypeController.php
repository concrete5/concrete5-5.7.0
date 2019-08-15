<?php
namespace Concrete\Core\Authentication\Type\OAuth\OAuth1a;

use Concrete\Core\Authentication\Type\OAuth\GenericOauthTypeController;
use Concrete\Core\Routing\RedirectResponse;
use OAuth\Common\Exception\Exception;
use Concrete\Core\User\User;

abstract class GenericOauth1aTypeController extends GenericOauthTypeController
{
    public function handle_authentication_attempt()
    {
        $token = $this->getService()->requestRequestToken();
        $url = $this->getService()->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
        id(new RedirectResponse((string) $url))->send();
        exit;
    }

    public function handle_authentication_callback()
    {
        $user = new User();
        if ($user && !$user->isError() && $user->isLoggedIn()) {
            $this->handle_attach_callback();
        }
        $token = \Concrete\Core\Http\Request::getInstance()->get('oauth_token');
        $verifier = \Concrete\Core\Http\Request::getInstance()->get('oauth_verifier');

        $token = $this->getService()->requestAccessToken($token, $verifier);
        $this->setToken($token);

        if ($token) {
            try {
                $user = $this->attemptAuthentication();
                if ($user) {
                    return $this->completeAuthentication($user)->send();
                } else {
                    $this->showError(
                        t('No local user account associated with this user, please log in with a local account and connect your account from your user profile.'));
                }
            } catch (Exception $e) {
                $this->showError($e->getMessage());
            } catch (\Exception $e) {
                $this->showError(t('An unexpected error occurred.'));
            }
        } else {
            $this->showError(t('Failed to complete authentication.'));
        }
        exit;
    }

    public function handle_attach_attempt()
    {
        $token = $this->getService()->requestRequestToken();
        $url = $this->getService()->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
        id(new RedirectResponse((string) $url))->send();
        exit;
    }

    public function handle_attach_callback()
    {
        $user = new User();
        if (!$user->isLoggedIn()) {
            id(new RedirectResponse(\URL::to('')))->send();
            exit;
        }

        $token = \Concrete\Core\Http\Request::getInstance()->get('oauth_token');
        $verifier = \Concrete\Core\Http\Request::getInstance()->get('oauth_verifier');

        $token = $this->getService()->requestAccessToken($token, $verifier);

        if ($token) {
            if ($this->bindUser($user, $this->getExtractor(true)->getUniqueId())) {
                $this->showSuccess(t('Successfully attached.'));
                exit;
            }
        }
        $this->showError(t('Unable to attach user.'));
        exit;
    }

    public function view()
    {
        // Nothing here.
    }

    /**
     * Method used to clean up.
     * This method must be defined, if it isn't needed, leave it blank.
     *
     * @param \Concrete\Core\User\User $u
     */
    public function deauthenticate(User $u)
    {
        // Nothing to do here.
    }

    /**
     * Test user authentication status.
     *
     * @param \Concrete\Core\User\User $u
     *
     * @return bool Returns true if user is authenticated, false if not
     */
    public function isAuthenticated(User $u)
    {
        return $u->isLoggedIn();
    }
}
