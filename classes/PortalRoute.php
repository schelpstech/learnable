<?php


/**
 * Immutable, request-scoped navigation state for the learner/instructor portal.
 *
 * Route values deliberately live in the URL, not in the session. This makes
 * pages bookmarkable and prevents one browser tab from changing another tab's
 * active subject, result, or resource.
 */
final class PortalRoute
{
    private $page;
    private $view;
    private $params;

    private function __construct($page, $view, array $params = array())
    {
        $this->page = $page;
        $this->view = $view;
        $this->params = $params;
    }

    public static function fromRequest(array $query, $userType)
    {
        $page = self::token(isset($query['pageid']) ? $query['pageid'] : 'index', 'pageid');
        $roles = array(
            'index' => array('Learner', 'Instructor'),
            'overview' => array('Learner', 'Instructor'),
            'subject' => array('Learner', 'Instructor'),
            'note' => array('Learner', 'Instructor'),
            'task' => array('Learner', 'Instructor'),
            'work' => array('Learner', 'Instructor'),
            'scheme' => array('Learner', 'Instructor'),
            'result' => array('Learner'),
            'midterm_result' => array('Learner'),
            'payment' => array('Learner'),
            'resources' => array('Instructor'),
            'class_manager' => array('Instructor'),
            'scoresheet' => array('Instructor'),
            'manage_learner' => array('Instructor'),
        );

        if (!isset($roles[$page])) {
            throw new InvalidArgumentException('Unknown portal route.');
        }
        if (!in_array($userType, $roles[$page], true)) {
            throw new RuntimeException('This page is not available for your account.');
        }

        if ($page === 'index' || $page === 'overview') {
            return new self($page, 'redirect');
        }

        $params = array();
        foreach (array('subjectid', 'ref', 'instance', 'item', 'item_ref') as $name) {
            if (isset($query[$name]) && $query[$name] !== '') {
                $params[$name] = self::value($query[$name], $name);
            }
        }

        $selectorPages = array('subject');
        if (($page === 'note' || $page === 'task' || $page === 'work') && isset($params['subjectid'])) {
            $selectorPages[] = $page;
        }
        if ($page === 'result' && isset($params['instance']) && $params['instance'] === 'select') {
            $selectorPages[] = $page;
        }

        $view = in_array($page, $selectorPages, true) ? 'selector' : 'viewer';
        self::validateCombination($page, $view, $params);

        return new self($page, $view, $params);
    }

    private static function validateCombination($page, $view, array $params)
    {
        if ($page === 'subject') {
            return;
        }
        if ($view === 'selector' && in_array($page, array('note', 'task', 'work'), true) && !isset($params['subjectid'])) {
            throw new InvalidArgumentException('A subject is required for this page.');
        }
        if (in_array($page, array('note', 'task', 'work', 'scheme', 'result', 'midterm_result'), true)
            && $view === 'viewer' && !isset($params['ref'])) {
            throw new InvalidArgumentException('A record reference is required for this page.');
        }
        if ($page === 'payment' && (!isset($params['instance']) || !in_array($params['instance'], array('bill', 'transaction', 'payment'), true))) {
            throw new InvalidArgumentException('Unknown payment page.');
        }
        if ($page === 'manage_learner' && !isset($params['instance'])) {
            throw new InvalidArgumentException('A learner reference is required.');
        }
        if ($page === 'resources') {
            $items = array('add_topic', 'add_note', 'add_task', 'add_cbt', 'modify_topic', 'modify_note', 'modify_task');
            if (!isset($params['item']) || !in_array($params['item'], $items, true)) {
                throw new InvalidArgumentException('Unknown resource action.');
            }
            if (strpos($params['item'], 'modify_') === 0 && !isset($params['item_ref'])) {
                throw new InvalidArgumentException('A resource reference is required.');
            }
        }
    }

    private static function token($value, $name)
    {
        $value = is_string($value) ? trim($value) : '';
        if ($value === '' || !preg_match('/^[a-z_]+$/', $value)) {
            throw new InvalidArgumentException('Invalid ' . $name . '.');
        }
        return $value;
    }

    private static function value($value, $name)
    {
        $value = is_string($value) || is_numeric($value) ? trim((string) $value) : '';
        if ($value === '' || strlen($value) > 100 || !preg_match('/^[A-Za-z0-9_.\- \/]+$/', $value)) {
            throw new InvalidArgumentException('Invalid ' . $name . '.');
        }
        return $value;
    }

    public function page()
    {
        return $this->page;
    }

    public function view()
    {
        return $this->view;
    }

    public function param($name, $default = null)
    {
        return array_key_exists($name, $this->params) ? $this->params[$name] : $default;
    }
}
