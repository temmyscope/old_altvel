<?php
namespace app\controller;
use app\Auth;
use app\model\{
	Controller, Model, Notification, Strings
};

class AuthController extends Controller{

	public function __construct($controller, $action){
		parent::__construct($controller, $action);
		$this->load_model('Auth');
	}

	public function indexEndPoint(){
		view('auth.index');
	}

	public function aboutEndPoint(){
		if($this->request->isSecured()){
			$this->request->validate([
				'email' => [ 'display' => 'E-mail', 'required' => true ],
				'feedback' => [ 'display' => 'FeedBack', 'required' => true],
			]);
			if($request->passed()){
				$contact = $this->AuthModel->rawInsert('contact_us', [
					'email' => post('email'),
					'feedback' => post('feedback'),
					'created_at' => generateTime()
				]);
				$this->request->status('success', 'We have received your message. Thanks.');
			}
		}
		view('auth.about');	
	}

	public function registerEndPoint(){
		if($this->request->isSecured()){
			$this->request->validate([
				'email' => [ 'display' => 'E-mail', 'required' => true, 'valid_email' => true, 'unique' => true ],
				'password' => [ 'display' => 'Password', 'required' => true, 'min' => 8 ],
				'verify_password' => [ 'display' => 'Verify Password', 'required' => true, 'min' => 8, 'is_same_as' => 'password' ],
				'name' => [ 'display' => 'Name', 'required' => true]
			], 'Users');
			if($this->request->passed()){
				$key = Strings::generateToken();
				$reg = $this->AuthModel->save([
					'name' => post('name'),
					'email' => post('email'),
					'password' => Strings::Encrypt(post('password')),
					'backup_pass' => Strings::en_mcrypt(post('password')),
					'activation' => $key,
					'created_at' => Strings::generateTime()
				]);
				if($reg !== false){
					Notification::AccountCreated(post('email'), $key);
					$this->request->status('success', 'Your account has beeen created. check your e-mail to activate your account.');
					redirect('login');
				}
			}
		}
		view('auth.register');
	}

	public function activateEndPoint($email, $key){
		$user = $this->AuthModel->findByEmail($email);
		if($email === $user->email && $user->activation === $key){
			$this->AuthModel->update(['verified' => 'true'], ['id' => $user->id]);
			$user = ''; $this->request->status('success', 'Your Account has been created and Activated. Please Login');
			redirect('login');
		}else{
			redirect('errors/bad');
		}
	}

	public function loginEndPoint(){
		if($this->request->isSecured()){
		$this->request->validate([
				'email' => [ 'display' => 'E-mail', 'required' => true ],
				'password' => [ 'display' => 'Password', 'required' => true, 'min' => 8 ]
			]);
			if($this->request->passed()){
				$user = $this->AuthModel->findByEmail(post('email'));
				if($user && Strings::verify(post('password'), $user->password)){
					$remember = (is_null(post('remember_me'))) ? false : true;
					$auth = new Auth((int) $user->id);
					$auth->login($remember);
					resume();
				}else{
					$this->request->status('error', 'There is an error with your email or password.');
				}
			}
		}
		view('auth.login');
	}

	public function forgot_passwordEndPoint(){
		if($this->request->isSecured()){
			$this->request->validate([
				'email' => [ 'display' => 'E-mail', 'required' => true ],
			]);
			if($this->request->passed()){
				$password = $this->AuthModel::columns('backup_pass')->where('email', '=', Request::post('email'))->fetch(MODEL::FETCH_OBJECT);
				if($password !== false && !empty($password) ){
					Notification::AccountCreated(post('email'), Strings::de_mcrypt($password->backup_pass));
					$this->request->status('success', 'A password reset link has been sent to your E-mail.');
				}else{

				}
			}
		}
		view('auth.forgot_password');
	}

	public function logoutEndPoint(){
		if(Auth::thisUser() !== NULL && Auth::thisUser()->logout()){}
		redirect('');
	}
}