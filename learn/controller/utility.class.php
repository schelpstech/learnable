<?php
class Utility
{
    public function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function generateRandomText($length)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function dayinterval($start, $end)
    {
        $interval = date_diff($start, $end);
        return $interval->format('%R%a days');
    }
    public function money($amount)
    {
        $regex = "/\B(?=(\d{3})+(?!\d))/i";
        return "&#8358;" . preg_replace($regex, ",", $amount);
    }

    public function grader($score)
    {
        if ($score >= 75) {
            $grade = "A";
            $remarks = "Excellent";
          } elseif ($score>= 65) {
            $grade = "B";
            $remarks = "Very Good";
          } elseif ($score >= 50) {
            $grade = "C";
            $remarks = "Moderate";
          } elseif ($score >= 45) {
            $grade = "D";
            $remarks = "Fair";
          } elseif ($score >= 40) {
            $grade = "E";
            $remarks = "Needs Help";
          } else {
            $grade = "F";
            $remarks = "Needs Help";
          }
        return $grade.  "  -  " .$remarks;
    }

    public function RemoveSpecialChar($str) {
      $result = str_replace( array( '\'', '"', ',' , ';', '<', '>', '/' ), '', $str);
      return $result;
      }

}
?>