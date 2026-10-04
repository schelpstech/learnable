<?php

/** Main-administrator staff management, including role changes and audit history. */
final class StaffAccountService extends SchoolService
{
    private function requireAdministrator($actor)
    {
        $access = new StaffAccess($this->db, $_SESSION);
        if ($access->role() !== 'administrator' || $access->username() !== $actor) {
            throw new RuntimeException('Only the main administrator can manage staff accounts.');
        }
    }

    public function save(array $input, $actor, $creating)
    {
        $this->requireAdministrator($actor);
        $username = self::text($input[$creating ? 'stuname' : 'stnamed'] ?? '', 64);
        $name = self::text($input['stname'] ?? '', 255);
        $email = self::text($input[$creating ? 'stmail' : 'stemail'] ?? '', 244, true);
        $phone = self::text($input['stfone'] ?? '', 16, true);
        $role = $input['role'] ?? '';
        if (!is_string($role) || !isset(StaffAccess::STAFF_ROLES[$role])) {
            throw new InvalidArgumentException('Select a valid staff role.');
        }
        $password = $input['stpwd'] ?? '';
        if (!is_string($password) || (($creating || $password !== '') && (strlen($password) < 8 || strlen($password) > 64))) {
            throw new InvalidArgumentException('Password must contain between 8 and 64 characters.');
        }
        return $this->locked('staff:'.$username, function() use($username,$name,$email,$phone,$role,$password,$actor,$creating) {
            $before = $this->one('SELECT sname,staffname,semail,sfone,role,status FROM lhpstaff WHERE sname=?', [$username]);
            if ($creating) {
                if ($before || $this->one('SELECT dname FROM `123admin` WHERE dname=?', [$username]) || $this->one('SELECT uname FROM lhpuser WHERE uname=?', [$username])) {
                    throw new RuntimeException('That username is already in use. Choose another username.');
                }
                $this->execute('INSERT INTO lhpstaff (sname,staffname,spwd,semail,sfone,role,status) VALUES (?,?,?,?,?,?,1)', [$username,$name,password_hash($password,PASSWORD_DEFAULT),$email,$phone,$role]);
            } else {
                if (!$before) throw new RuntimeException('Staff account not found.');
                $this->execute('UPDATE lhpstaff SET staffname=?,semail=?,sfone=?,role=? WHERE sname=?', [$name,$email,$phone,$role,$username]);
                if ($password !== '') $this->execute('UPDATE lhpstaff SET spwd=? WHERE sname=?', [password_hash($password,PASSWORD_DEFAULT),$username]);
            }
            $after = $this->one('SELECT sname,staffname,semail,sfone,role,status FROM lhpstaff WHERE sname=?', [$username]);
            $this->audit('staff',$username,$actor,$creating ? 'created' : 'updated',$before,$after);
        });
    }

    public function changeStatus($username,$status,$actor)
    {
        $this->requireAdministrator($actor);
        $before=$this->one('SELECT sname,role,status FROM lhpstaff WHERE sname=?',[self::text($username,64)]);
        if (!$before) throw new RuntimeException('Staff account not found.');
        $status=self::integer($status,'Status',0,1);
        $this->execute('UPDATE lhpstaff SET status=? WHERE sname=?',[$status,$username]);
        $this->audit('staff',$username,$actor,'status_changed',$before,['status'=>$status]);
    }

    public function remove($username,$actor)
    {
        $this->requireAdministrator($actor);
        $before=$this->one('SELECT sname,staffname,role,status FROM lhpstaff WHERE sname=?',[self::text($username,64)]);
        if (!$before) throw new RuntimeException('Staff account not found.');
        $this->execute('DELETE FROM lhpstaff WHERE sname=?',[$username]);
        $this->audit('staff',$username,$actor,'deleted',$before,null);
    }
}
