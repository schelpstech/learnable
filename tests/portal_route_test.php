<?php

require __DIR__ . '/../classes/PortalRoute.php';

function expect_route(array $query, $role, $page, $view)
{
    $route = PortalRoute::fromRequest($query, $role);
    if ($route->page() !== $page || $route->view() !== $view) {
        throw new RuntimeException('Unexpected route result for ' . json_encode($query));
    }
    return $route;
}

expect_route(array('pageid' => 'subject'), 'Learner', 'subject', 'selector');
$note = expect_route(array('pageid' => 'note', 'subjectid' => 'MTH-01'), 'Learner', 'note', 'selector');
if ($note->param('subjectid') !== 'MTH-01') {
    throw new RuntimeException('Subject parameter was not retained.');
}
expect_route(array('pageid' => 'task', 'ref' => '42'), 'Learner', 'task', 'viewer');
expect_route(array('pageid' => 'resources', 'item' => 'modify_note', 'item_ref' => '8'), 'Instructor', 'resources', 'viewer');

$rejected = 0;
foreach (array(
    array(array('pageid' => '../../conf'), 'Learner'),
    array(array('pageid' => 'resources', 'item' => 'add_note'), 'Learner'),
    array(array('pageid' => 'note', 'ref' => '\"><script>'), 'Learner'),
    array(array('pageid' => 'work'), 'Learner'),
) as $case) {
    try {
        PortalRoute::fromRequest($case[0], $case[1]);
    } catch (Exception $exception) {
        $rejected++;
    }
}

if ($rejected !== 4) {
    throw new RuntimeException('Unsafe route input was accepted.');
}

echo "Portal route tests passed.\n";
