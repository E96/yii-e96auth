<?php
// vim: sw=4:ts=4:noet:sta:
namespace E96Auth;
use \Yii;
use \CAction;
use \EAuthUserIdentity;
use \CException;

/**
 * Внутренняя аутентификация через OAauth
 */
class LoginAction extends \CAction {

	/**
	 * @var string название компонента с пользователем
	 */
	public $userComponent = 'user';

	/**
	 * @var string название компонента с EAuth
	 */
	public $eauthComponent = 'eauth';

	/**
	 * @var string название сервиса для авторизации
	 */
	public $eauthService = 'e96_auth';

	public function run() {
		$eauth = Yii::app()->getComponent($this->eauthComponent)
			->getIdentity($this->eauthService);
		$user = Yii::app()->getComponent($this->userComponent);

		if ($eauth->authenticate()) {
			$identity = new EAuthUserIdentity($eauth);

			if ($identity->authenticate()) {
				$user->login($identity);
				$this->controller->redirect($user->returnUrl);
			} else {
				$eauth->cancel();
			}
			throw new CException('Неведомая фигня');
		} else {
			throw new CException('Неведомая ошибка при авторизации');
		}
	}
}
