<?php

/** Request-scoped permissions, resolved from current database records. */
final class StaffAccess
{
    public const STAFF_ROLES = ['t' => 'Teaching Staff', 'b' => 'Bursary Staff', 'r' => 'Registry Admin'];
    private $identity;

    public function __construct(PDO $db, array $session)
    {
        $this->identity = ['role' => 'guest', 'username' => '', 'name' => ''];
        $kind = $session['auth_account_type'] ?? '';
        $username = $session['auth_username'] ?? $session['unamed'] ?? $session['active'] ?? '';
        if (!is_string($username) || $username === '') { return; }
        // Older authenticated sessions are classified from their original session markers.
        if ($kind === '' && !empty($session['unamed'])) {
            $q = $db->prepare('SELECT dname FROM `123admin` WHERE dname=?'); $q->execute([$username]);
            $admin = $q->fetchColumn();
            $q = $db->prepare('SELECT sname FROM lhpstaff WHERE sname=?'); $q->execute([$username]);
            $staff = $q->fetchColumn();
            if ($admin && $staff) { return; } // Ambiguous old sessions must sign in again.
            $kind = $admin ? 'admin' : 'staff';
        } elseif ($kind === '') {
            $kind = ($session['user_type'] ?? '') === 'Learner' ? 'learner' : 'staff';
        }
        if ($kind === 'admin' && ($session['unamed'] ?? '') === $username) {
            $q = $db->prepare('SELECT dname FROM `123admin` WHERE dname=?'); $q->execute([$username]);
            if ($q->fetchColumn()) { $this->identity = ['role'=>'administrator','username'=>$username,'name'=>$username]; }
        } elseif ($kind === 'staff') {
            $q = $db->prepare('SELECT sname,staffname,role,status FROM lhpstaff WHERE sname=?'); $q->execute([$username]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            $role = ['t'=>'teacher','b'=>'bursary','r'=>'registry'][$row['role'] ?? ''] ?? 'guest';
            if ($row && (int)$row['status'] === 1 && $role !== 'guest') {
                $this->identity = ['role'=>$role,'username'=>$username,'name'=>$row['staffname'] ?: $username];
            }
        } elseif ($kind === 'learner' && ($session['active'] ?? '') === $username) {
            $q = $db->prepare('SELECT fname FROM lhpuser WHERE uname=? AND status=1'); $q->execute([$username]);
            if ($name = $q->fetchColumn()) { $this->identity = ['role'=>'learner','username'=>$username,'name'=>$name]; }
        }
    }

    public function role() { return $this->identity['role']; }
    public function username() { return $this->identity['username']; }
    public function name() { return $this->identity['name']; }
    public function label() { return ['administrator'=>'Administrator','registry'=>'Registry Admin','bursary'=>'Bursary Staff','teacher'=>'Teaching Staff','learner'=>'Learner'][$this->role()] ?? 'Guest'; }
    public function academic() { return in_array($this->role(), ['administrator','registry'], true); }
    public function financial() { return in_array($this->role(), ['administrator','bursary'], true); }

    public function canFile($file)
    {
        if ($this->role() === 'administrator') { return true; }
        $files = require dirname(__DIR__) . '/config/staff_permissions.php';
        return in_array($file, $files[$this->role()] ?? [], true);
    }

    public function canRoute($route)
    {
        $routes = require dirname(__DIR__) . '/config/admin_routes.php';
        return isset($routes[$route]) && $this->canFile($routes[$route]);
    }

    public function requireAdminRequest()
    {
        $file = basename($_SERVER['SCRIPT_NAME'] ?? '');
        if ($file === 'index.php') {
            $route = $_GET['route'] ?? 'dashboard';
            $routes = require dirname(__DIR__) . '/config/admin_routes.php';
            if ($this->role() === 'administrator' && (!is_string($route) || !isset($routes[$route]))) {
                http_response_code(404); exit('Page not found.');
            }
            $allowed = is_string($route) && $this->canRoute($route);
        } else { $allowed = $this->canFile($file); }
        if (!$allowed) { $this->deny(); }
    }

    public function deny()
    {
        if ($this->role() === 'guest' && empty($_SESSION['unamed']) && empty($_SESSION['active'])) {
            header('Location: ../admin.php'); exit;
        }
        http_response_code(403);
        header('Cache-Control: no-store');
        exit('Access denied. This page or action is not available for your account.');
    }

    public static function clearAuthentication()
    {
        foreach (['unamed','active','user_type','stnamed','studnamed','classd','auth_account_type','auth_username','admin_csrf','portal_csrf','profile_csrf'] as $key) { unset($_SESSION[$key]); }
    }
}
