<?php

namespace Kodeplusdev\Kandylaravel;

class KandyController extends \BaseController
{

    /**
     * Get name for contacts
     *
     * @return mixed
     */
    public function getNameForContact()
    {
        if (!isset($_GET['data'])) {
            return Response::make('Your request is invalid.', 403);
        }
        $contacts = $_GET['data'];
        foreach ($contacts as &$contact) {
            $user = KandyUsers::whereemail($contact['contact_email'])->first();
            if (empty($user)) {
                $displayName = "";
            } else {
                $kandylaravel = new Kandylaravel();
                $displayName = $kandylaravel->getDisplayName($user->id);
            }
            $contact['display_name'] = $displayName;
        }
        return \Response::json($contacts, 200);

    }

    /**
     * Get name for chat content
     *
     * @return mixed
     */
    public function getNameForChatContent()
    {
        if (!isset($_GET['data'])) {
            return Response::make('Your request is invalid.', 403);
        }
        $messages = $_GET['data'];
        foreach ($messages as &$message) {
            $sender = $message['sender'];
            $user = KandyUsers::whereuser_id($sender['user_id'])->first();
            if (empty($user)) {
                $displayName = "";
            } else {
                $kandylaravel = new Kandylaravel();
                $displayName = $kandylaravel->getDisplayName($user->id);
            }
            $sender['display_name'] = $displayName;
            $sender['contact_user_name'] = $sender['full_user_id'];
            $message['sender'] = $sender;
        }
        return \Response::json($messages, 200);
    }

    public function getUsersForSearch()
    {
        if (!isset($_GET['q'])) {
            return \Response::make('Your request is invalid.', 403);
        }
        $users = array();

        $search = $_GET['q'];
        $kandyLaravel = new Kandylaravel();
        $kandyUserTable = \Config::get('kandylaravel::kandy_user_table');
        $mainUserTable = \Config::get('kandylaravel::user_table');
        $displayNameColumn = $kandyLaravel->getColumnForDisplayName('m');
        $mainUserTablePrimaryKey = $kandyLaravel->getMainUserIdColumn();

        $sql = "SELECT CONCAT(k.user_id, '@', k.domain_name)as kandyFullUsername, $displayNameColumn as mainUsername
                FROM  $mainUserTable m, $kandyUserTable k
                WHERE m.$mainUserTablePrimaryKey = k.main_user_id
                HAVING mainUsername like '%$search%'";
        $data = \DB::select($sql);
        foreach ($data as $user) {
            $userToAdd = array(
                'id' => $user->kandyFullUsername,
                'text' => $user->mainUsername
            );
            array_push($users, $userToAdd);
        }
        $result = array('results' => $users, 'more' => false);
        return \Response::json($result, 200);
    }

    /*public function __construct()
    {
        //updated: prevents re-login.
        $this->beforeFilter('guest', ['only' => ['getLogin']]);
        $this->beforeFilter('auth', ['only' => ['getLogout']]);
    }

    public function getIndex()
    {
        $posts = Post::orderBy('id', 'desc')->paginate(10);
        //$posts->getEnvironment()->setViewName('pagination::simple');
        $this->layout->title = 'Home Page | Laravel 4 Blog';
        $this->layout->main = View::make('home')->nest('content', 'index', compact('posts'));
    }

    public function getSearch()
    {
        $searchTerm = Input::get('s');
        $posts = Post::whereRaw('match(title,content) against(? in boolean mode)', [$searchTerm])
            ->paginate(10);
        //$posts->getEnvironment()->setViewName('pagination::slider');
        $posts->appends(['s' => $searchTerm]);
        $this->layout->with('title', 'Search: ' . $searchTerm);
        $this->layout->main = View::make('home')
            ->nest('content', 'index', ($posts->isEmpty()) ? ['notFound' => true] : compact('posts'));
    }

    public function getLogin()
    {
        $this->layout->title = 'login';
        $this->layout->main = View::make('login');
    }

    public function postLogin()
    {
        $credentials = [
            'username' => Input::get('username'),
            'password' => Input::get('password')
        ];
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->passes()) {
            try {
                if (Auth::attempt($credentials)) {
                    return Redirect::to('admin/dash-board');
                }
            } catch (Toddish\Verify\UserNotFoundException $e) {
            } catch (Toddish\Verify\UserPasswordIncorrectException $e) {
            } catch (Toddish\Verify\UserUnverifiedException $e) {
            } catch (Toddish\Verify\UserDisabledException $e) {
            } catch (Toddish\Verify\UserDeletedException $e) {
            } catch (Exception $e) {
                $data[Msg::key()] = Msg::error($e->getMessage());
            }
            return Redirect::back()->withInput()->with('failure', 'username or password is invalid!');
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function getSignup()
    {
        $this->layout->title = 'signup';
        $this->layout->main = View::make('signup');
    }

    public function postSignup()
    {
        $user = [
            'username' => Input::get('username'),
            'password' => Input::get('password'),
            'email' => Input::get('email'),
            'password_confirmation' => Input::get('password_confirmation')
        ];
        $rules = [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|confirmed|min:6'
        ];
        $validator = Validator::make($user, $rules);
        if ($validator->passes()) {
            // Create a new User
            $user = new User();
            $user->assignAttributes();
            $user->createUser();

            $credentials = [
                'username' => $user->username,
                'password' => Input::get('password')
            ];

            if (Auth::attempt($credentials)) {
                return Redirect::to('admin/dash-board');
            }
            return Redirect::back()->withInput()->with('failure', 'username or password is invalid!');
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function getLogout()
    {
        Auth::logout();
        $logoutScript = KandyLaravel::logout();
        return Redirect::to('/')->with('kandyLogout', $logoutScript);
    }*/

}
