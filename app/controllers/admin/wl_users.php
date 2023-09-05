<?php

class wl_users__admin extends Controller {

    public function _remap($method, $data = array())
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            if (empty($data)) $data = null;
            return $this->$method($data);
        } else
            $this->index($method);
    }

    public function index($user_id)
    {
        $this->page->name = 'Users';
        if($this->user->can('profile'))
        {
            if($user_id = $this->data->uri(2))
            {
                $this->load->model('wl_user_model');
                if($user = $this->wl_user_model->getInfo($user_id))
                {
                    // $wl_user_status = $this->db->getAllData('wl_user_status');
                    $wl_user_types = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');

                    $this->page->name = $user->name;
                    $this->page->breadcrumbs = array('Users' => 'wl_users', $user->name => '');
                    $this->load->admin_view('wl_users/edit_view', compact('user', 'wl_user_types'));
                }
                else
                    $this->load->admin_view('404_view');
            }
            else
            {
                $this->page->breadcrumbs = array('Users' => '');
                $this->load->admin_view('wl_users/list_view');
            }
        }
        else
        {
            // header('HTTP/1.0 403 Forbidden');
            $this->page->name = '403 Forbidden';
            $this->load->admin_view('403_view');
        }
    }

    public function my()
    {
        $this->page->name = '<small>User</small> ' . $this->user->name;
        $this->page->title = $this->user->name;
        $this->page->breadcrumbs = array('Users' => 'wl_users', $this->user->name => '');
        $this->load->admin_view('wl_users/profile_view');
    }

    public function data()
    {
        $result = ["recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
        $result['draw'] = $this->data->get('draw') ?? 1;

        $where = [];
        if ($this->user->type_id > 2) {
            $where['type_id'] = '>2';
        }

        $result['recordsTotal'] = $result['recordsFiltered'] = $this->db->getCount('wl_users', $where);

        if ($this->user->type_id < 3) {
            if ($type_id = $this->data->get('type_id'))
                if (is_numeric($type_id) && $type_id > 0)
                    $where['type_id'] = $type_id;
        }

        if (!empty($_GET['search']['value'])) {
            $search = htmlspecialchars(trim($_GET['search']['value']), ENT_QUOTES);
            $list = [];
            if (is_numeric($search))
                $list[] = 'u.id = ' . $search;
            if (mb_strlen($search) > 2) {
                foreach (['name', 'email', 'phone'] as $field) {
                    $list[] = "u.{$field} LIKE '%{$search}%'";
                }
            }
            if (empty($list))
                $this->load->json($result);
            $where['&'] = implode(' OR ', $list);
        }

        $start = $this->data->get('start');
        $length = $this->data->get('length');
        if (!is_numeric($start) || $start < 0)
            $start = 0;
        if (!is_numeric($length) || $length <= 0)
            $start = 25;

        $order = 'id DESC';
        if (!empty($_GET['order']['0']['column'])) {
            $order_col_id = $_GET['order']['0']['column'];
            if (!empty($_GET['columns'][$order_col_id]['data'])) {
                $order_col_name = $_GET['columns'][$order_col_id]['data'];
                $order_col_dir = $_GET['order']['0']['dir'];
                if (in_array($order_col_name, ['id', 'name', 'email', 'phone', 'leader_id', 'type_id', 'registered'])) {
                    $order = $order_col_name . ' ' . $order_col_dir;
                }
            }
        }

        $data = $this->db->select('wl_users as u', 'id, name, email, phone, leader_id, type_id, registered', $where)
            ->join('wl_user_types as t', 'title as type_title', '#u.type_id')
            ->join('wl_users as l', 'name as leader_name', '#u.leader_id')
            ->order($order)
            ->limit($start, $length)
            ->get('array', false);
        if ($data)
            foreach ($data as $info) {
                $row = (array) $info;
                $row["DT_RowId"] = "row_" . $info->id;
                $row['eye'] = '<a class="btn btn-xs btn-info" href="' . SERVER_URL . 'admin/wl_users/' . $info->id . '" data-id="' . $info->id . '" data-bs-toggle="modal" data-bs-target="#userDetalModal"><i class="far fa-eye"></i></a>';
                $row['created_at'] = date('d.m.Y H:i', $info->registered);
                $row['leader_name'] = $info->leader_id ? '#'. $info->leader_id. ' '. $info->leader_name : 'None';
                switch ($info->type_id) {
                    case 1:
                        $row['type_title'] = '<span class="text-warning"><i class="fas fa-crown"></i> ' . $info->type_title . '</span>';
                        break;
                    case 2:
                        $row['type_title'] = '<span class="text-info"><i class="fas fa-user-tie"></i> ' . $info->type_title . '</span>';
                        break;
                    case 3:
                        $row['type_title'] = '<span class="text-success"><i class="fas fa-user-secret"></i> ' . $info->type_title . '</span>';
                        break;
                    case 4:
                        $row['type_title'] = '<span class="text-danger"><i class="fas fa-user-md"></i> ' . $info->type_title . '</span>';
                        break;
                }
                $result['data'][] = $row;
            }

        if (!empty($where))
            $result['recordsFiltered'] = $this->db->get('count');
        $this->db->clear();

        $this->load->json($result);
    }

    public function add()
    {
        $res = ['status' => 'error'];
        if($this->user->can('profile'))
        {
            $this->load->library('validator');
            $this->validator->setRules('E-mail', $this->data->post('email'), 'required|email');
            $this->validator->setRules('Name', $this->data->post('name'), 'required');
            if($this->data->post('selectPassword') == 'set')
                $this->validator->setRules('User password', $this->data->post('password'), 'required');

            if($this->validator->run())
            {
                $this->load->model('wl_user_model');
                $info = $this->data->prepare(['email', 'name', 'phone', 'type_id', 'password']);
                $info['status_id'] = 1;
                $info['leader_id'] = 0;
                if(empty($info['type_id']) || $this->user->type_id == 2)
                    $info['type_id'] = 3; // for pcr
                if($this->data->post('selectPassword') == 'by_email')
                    $info['password'] = bin2hex(openssl_random_pseudo_bytes(4));
                $info['auth'] = false;
                if($user = $this->wl_user_model->create($info, [], "From admin by: #{$_SESSION['user']->id} {$_SESSION['user']->name}"))
                {
                    $res = ['status' => 'success', 'text' => 'User created'];

                    // to do wl_user_permissions

                    if ($this->data->post('selectPassword') == 'by_email')
                    {
                        $this->load->library('mail');
                        $user->password = $info['password'];
                        if($this->mail->sendTemplate('signup/by_admin_sent_password', $user->email, $user))
                            $res['text'] .= '. Password send by e-mail';
                    }
                }
                else
                    $res['text'] = $this->wl_user_model->user_errors;
            }
            else
                $res['text'] = $this->validator->getErrors();
        }
        $this->load->json($res);
    }

    public function edit()
    {
        $res = ['status' => 'error', 'text' => 'Access denied'];
        if ($this->user->can('profile'))
        {
            $field = $this->data->post('field');
            $value = $this->data->post('value');

            if($user_id = $this->data->post('user_id'))
            {
                $this->load->model('wl_user_model');
                if($user = $this->wl_user_model->getInfo($user_id))
                {
                    if(in_array($field, ['email']))
                    {
                        if ($user2 = $this->wl_user_model->getInfo([$field => $value]))
                        {
                            $res['text'] = "User #{$user2->id} {$user2->name} use `{$field}` {$value}";
                        }
                        else
                        {
                            $this->wl_user_model->update($user->id, [$field => $value]);
                            $res = ['status' => 'success', 'text' => $field . ' updated'];
                        }
                    }
                    if(in_array($field, ['name', 'phone', 'type_id', 'leader_id']))
                    {
                        $this->wl_user_model->update($user->id, [$field => $value]);
                        $res = ['status' => 'success', 'text' => $field . ' updated'];
                    }
                } 
                else
                    $res['text'] = 'User not find!';
            }
            else
                $res['text'] = 'POST `user_id` param required!';

            $this->load->model('wl_user_model');
        }
        $this->load->json($res);
    }

    public function changePassword()
    {
        $this->load->model('wl_user_model');
        $this->load->library('validator');
        $res = ['status' => 'error', 'text' => 'Access denied'];
        if(empty($_POST['user_id']))
        {
            if(!empty($_POST['password']) && !empty($_POST['new-password']) && !empty($_POST['re-new-password']))
            {
                if($this->wl_user_model->getPassword($this->user->id, $_POST['password'], $this->user->password)) {
                    $this->validator->setRules('New password', $this->data->post('new-password'), 'required|5..20');
                    $this->validator->password($this->data->post('new-password'), $this->data->post('re-new-password'));
                    if ($this->validator->run()) {
                        $this->user->register('reset', $this->user->password);
                        $_SESSION['user']->password = $this->wl_user_model->getPassword($this->user->id, $_POST['new-password']);
                        $this->wl_user_model->update($this->user->id, ['password' => $_SESSION['user']->password]);
                        $res = ['status' => 'success', 'text' => "New password set"];
                    } else
                        $res['text'] = $this->validator->getErrors();
                }
                else
                    $res['text'] = "Bad password";
            }
            else
                $res['text'] = "Password, New password and Re new password are required!";
        }
        else if($this->user->can('profile'))
        {
            if($user = $this->wl_user_model->getOne($this->data->post('user_id')))
            {
                if($user->type_id > 1 || $user->id == $this->user->id)
                {
                    $this->validator->setRules('New password', $this->data->post('password'), 'required|5..20');
                    if ($this->validator->run()) {
                        $this->user->register('reset', "Set password by #{$this->user->id} {$this->user->name}. Previous password: {$user->password}", $user->id);
                        $this->wl_user_model->update($user->id, ['password' => $this->wl_user_model->getPassword($user->id, $_POST['password'])]);
                        $res = ['status' => 'success', 'text' => "New password set"];
                    } else
                        $res['text'] = $this->validator->getErrors();
                }
                else
                    $res['text'] = 'You cannot set password to Admins';
            }

        }
        $this->load->json($res);
    }

    public function delete()
    {
        $res = ['status' => 'error', 'text' => 'Access denied'];
        if ($this->user->type_id == 1) // only admin
            if($user_id = $this->data->post('user_id'))
                if($password = $this->data->post('password'))
                {
                    if($user_id == $this->user->id)
                    {
                        $res['text'] = 'You cannot delete yourself!';
                        $this->load->json($res);
                    }

                    $this->load->model('wl_user_model');
                    if($this->wl_user_model->getPassword($this->user->id, $password, $this->user->password))
                    {
                        if($this->wl_user_model->delete($user_id))
                            $res = ['status' => 'success', 'text' => "User #{$user_id} has been deleted!"];
                    }
                    else
                        $res['text'] = 'Bad password';
                }
        $this->load->json($res);
    }

    public function confirm()
    {
        if($userId = $this->data->post('id'))
            if($user = $this->db->getAllDataById('wl_users', $userId))
                if($user->status != 1)
                {
                    $this->db->updateRow('wl_users', ['status' => 1], $user->id);
                    $this->db->register('confirmed', "Менеджер: #{$_SESSION['user']->id} {$_SESSION['user']->name}", $user->id);

                    $this->load->library('mail');
                    $this->mail->sendTemplate('signup/admin_confirm', $user->email, $user);
                }
        $this->redirect();
    }

    public function export()
    {
        if($_SESSION['user']->admin == 1)
        {
            $_SESSION['alias']->name = 'Експорт користувачів';
            $_SESSION['alias']->breadcrumb = array('Користувачі' => 'admin/wl_users', 'Експорт' => '');

            require_once APP_PATH.'controllers/signup.php';
            $signup = new Signup();

            $this->load->admin_view('wl_users/export_view', array('fields_additionall' => $signup->additionall));
        }
    }

    public function export_file()
    {
        if($_SESSION['user']->admin == 1 && !empty($_POST['types']) && !empty($_POST['fields']))
        {
            $this->db->select('wl_users as u', '*', array('type' => $_POST['types']))
                        ->join('wl_user_types', 'title as type_name', '#u.type')
                        ->join('wl_user_status', 'title as status_name', '#u.status');
            if($users = $this->db->get('array'))
            {
                $a = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
                $goodfields = array('id' => 'ID', 'email' => 'email', 'name' => "Ім'я користувача", 'type' => 'Тип', 'type_name' => 'Тип', 'status' => 'Статус', 'status_name' => 'Статус', 'registered' => 'Дата реєстрації', 'last_login' => 'Останній вхід');
                require_once APP_PATH.'controllers/signup.php';
                $signup = new Signup();
                if($signup->additionall)
                {
                    foreach ($_POST['fields'] as $field) {
                        if(in_array($field, $signup->additionall))
                        {
                            $goodfields[$field] = $field;
                            foreach ($users as $user) {
                                $user->$field = '';
                                $this->db->select('wl_user_info', 'value', array('user' => $user->id, 'field' => $field));
                                $this->db->order('date DESC');
                                $this->db->limit(1);
                                if($info = $this->db->get('single'))
                                    $user->$field = $info->value;
                            }
                        }
                    }
                }
        
                $this->load->library('PHPExcel');

                // Set document properties
                $this->phpexcel->getProperties()->setCreator(SITE_NAME)
                                             ->setLastModifiedBy(SITE_NAME)
                                             ->setTitle("Users ".SITE_NAME);

                $this->phpexcel->setActiveSheetIndex(0);
                $this->phpexcel->getActiveSheet()->setTitle('Users');

                $x = 0;
                foreach ($_POST['fields'] as $field) {
                    if(array_key_exists($field, $goodfields) && isset($users[0]->$field))
                    {
                        $y = 1;
                        $xy = $a[$x] . $y++;
                        $this->phpexcel->getActiveSheet()->setCellValue($xy, $goodfields[$field]);
                        foreach ($users as $user) {
                            $xy = $a[$x] . $y++;
                            if($field == 'registered' || $field == 'last_login')
                                $user->$field = date('d.m.Y H:i', $user->$field);
                            $this->phpexcel->getActiveSheet()->setCellValue($xy, $user->$field);
                        }
                        $x++;
                    }
                }
 
                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $this->phpexcel->setActiveSheetIndex(0);

                header('Cache-Control: max-age=0');
                // If you're serving to IE over SSL, then the following may be needed
                header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header ('Pragma: public'); // HTTP/1.0

                $date = date('dmY');
                if($_POST['file'] == 'xlsx')
                {
                    // Redirect output to a client’s web browser (Excel2007)
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.SITE_NAME.'-users-'.$date.'.xlsx"');

                    $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
                    $objWriter->save('php://output');
                }
                elseif($_POST['file'] == 'xls')
                {
                    // Redirect output to a client’s web browser (Excel5)
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="'.SITE_NAME.'-users-'.$date.'.xls"');

                    $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
                    $objWriter->save('php://output');
                }
                elseif($_POST['file'] == 'csv')
                {
                    // Redirect output to a client’s web browser (Excel5)
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="'.SITE_NAME.'-users-'.$date.'.csv"');

                    $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'CSV')->setDelimiter(',')
                                                                  ->setEnclosure('"')
                                                                  ->setSheetIndex(0);
                    $objWriter->save('php://output');
                }
            }
        }
        else
        {
            $_SESSION['notify'] = new stdClass();
            if($_SESSION['user']->admin != 1)
                $_SESSION['notify']->errors = 'Вигрузку користувачів може виконати тільки адміністратор';
            if(empty($_POST['types']))
                $_SESSION['notify']->errors = 'Увага! Вкажіть типи користувачів до вигрузки';
            if(empty($_POST['fields']))
            {
                if(isset($_SESSION['notify']->errors))
                    $_SESSION['notify']->errors .= '</p><p>Увага! Вкажіть поля вигрузки';
                else
                    $_SESSION['notify']->errors = 'Увага! Вкажіть поля вигрузки';
            }
            $this->redirect();
        }
        exit;
    }

    public function login_as_user()
    {
        if($this->userCan('profile') && ($id = $this->data->get('id')))
        {
            $this->load->model('wl_user_model');
            if($user = $this->wl_user_model->getInfo($id))
            {
                if($user->type == 1 || $user->type == 2 && !$_SESSION['user']->admin)
                {
                    $_SESSION['notify'] = new stdClass();
                    $_SESSION['notify']->errors = 'Адмін вхід до користувачів типу <strong>адміністратор</strong> заборонено';
                    $this->redirect();
                }
                $additionall = $_SESSION['user']->id .'. '.$_SESSION['user']->name;
                $_SESSION['user']->real_user_id = $_SESSION['user']->id;
                $this->db->updateRow('wl_users', ['reset_expires' => $user->id], $_SESSION['user']->id);
                $this->db->register('login_as_user', "Входив до #{$user->id}. {$user->name}");
                $this->wl_user_model->setSession($user, false);
                $this->db->register('login_as_user', $additionall);
                $this->redirect('profile');
            }
        }
        else
        {
            $this->page->name = '403 Forbidden';
            $this->load->admin_view('403_view');
        }
    }

}

?>