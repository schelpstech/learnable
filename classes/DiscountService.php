<?php

final class DiscountService extends SchoolService
{
    public function fees($term) {
        return $this->rows("SELECT f.*, COALESCE(u.fname,f.stdid) AS learner_name, COALESCE(c.classname,f.classid) AS class_name,
            CASE WHEN f.feeid='PreviousBalance' THEN 'Previous term balance' ELSE COALESCE(l.feename,f.feeid) END AS fee_name
            FROM lhpassignedfee f LEFT JOIN lhpuser u ON u.uname=f.stdid
            LEFT JOIN lhpclass c ON c.classid=f.classid LEFT JOIN lhpfeelist l ON l.feeid=f.feeid
            WHERE f.term=? AND f.status=1 ORDER BY u.fname,f.assid", array($term));
    }
    public function change($id, $amount, $expected, $actor, $remove = false, $confirmed = false) {
        $id = self::integer($id, 'Fee record', 1);
        $amount = self::integer($amount, 'Discount', 0);
        $expected = self::integer($expected, 'Previous discount', 0);
        if (!$remove && $amount === 0) throw new InvalidArgumentException('Enter a positive discount. To remove an existing discount, use Delete discount and confirm removal.');
        if ($remove && !$confirmed) throw new InvalidArgumentException('Confirm that you want to remove this discount.');
        return $this->locked('discount:'.$id, function () use ($id,$amount,$expected,$actor,$remove) {
            $fee = $this->one('SELECT * FROM lhpassignedfee WHERE assid=? AND status=1 FOR UPDATE', array($id));
            if (!$fee) throw new InvalidArgumentException('This active fee record was not found.');
            if ((int)$fee['discount'] !== $expected) throw new RuntimeException('This discount changed in another window. Refresh and review it first.');
            if ($amount > (int)$fee['amount']) throw new InvalidArgumentException('The discount cannot exceed the assigned fee.');
            if ($remove) $amount = 0;
            $this->audit('discount', $id, $actor, 'save_requested', array('discount'=>$expected), array('discount'=>$amount));
            $this->execute('UPDATE lhpassignedfee SET discount=? WHERE assid=? AND discount=?', array($amount,$id,$expected));
            $this->audit('discount', $id, $actor, $remove ? 'remove' : 'save', array('discount'=>$expected), array('discount'=>$amount));
        });
    }
}
