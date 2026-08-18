<?php
if (!isset($calendarContext) || !is_array($calendarContext)) {
    throw new RuntimeException('Calendar context is unavailable.');
}

if (!function_exists('calendar_escape')) {
    function calendar_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$calendar = $calendarContext['calendar'];
$eventsByDate = array();
$uniqueEvents = array();
foreach ($calendar['events'] as $event) {
    $eventsByDate[$event['date']][] = $event;
    if (!isset($uniqueEvents[$event['id']])) $uniqueEvents[$event['id']] = $event;
}
$firstTimestamp = strtotime($calendar['start']);
$daysInMonth = (int) date('t', $firstTimestamp);
$startOffset = (int) date('N', $firstTimestamp) - 1;
$today = date('Y-m-d');
$role = $calendarContext['role'];
$canManage = !empty($calendarContext['can_manage']);
$isAdminCalendar = $role === 'admin';
$calendarRouteBase = $calendarContext['month_url'];
?>

<div class="academic-calendar-shell">
    <?php if (!empty($calendarContext['notice'])): ?><div class="calendar-notice is-success"><i class="fa fa-check-circle" aria-hidden="true"></i><?php echo calendar_escape($calendarContext['notice']); ?></div><?php endif; ?>
    <?php if (!empty($calendarContext['error'])): ?><div class="calendar-notice is-error"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php echo calendar_escape($calendarContext['error']); ?></div><?php endif; ?>

    <header class="calendar-hero">
        <div>
            <span class="calendar-eyebrow">Academic planning</span>
            <h1>School calendar & timetable</h1>
            <p><?php echo $isAdminCalendar ? 'Plan school-wide milestones and keep every class aligned.' : 'See school activities and class sessions together in one clear view.'; ?></p>
        </div>
        <div class="calendar-hero-stat"><span>Active term</span><strong><?php echo calendar_escape($calendarContext['term']); ?></strong><small><?php echo calendar_escape($calendarContext['class_label']); ?></small></div>
    </header>

    <?php if (!empty($calendarContext['class_views'])): ?>
        <nav class="calendar-class-filter" aria-label="Assigned classes">
            <?php foreach ($calendarContext['class_views'] as $classView): ?>
                <a class="<?php echo $classView['active'] ? 'is-active' : ''; ?>" href="<?php echo calendar_escape($classView['url']); ?>"><?php echo calendar_escape($classView['label']); ?></a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <div class="calendar-layout<?php echo $canManage ? ' has-manager' : ''; ?>">
        <section class="calendar-card calendar-main" aria-labelledby="calendar-month-title">
            <div class="calendar-month-header">
                <div><span>This month</span><h2 id="calendar-month-title"><?php echo calendar_escape($calendar['label']); ?></h2></div>
                <nav aria-label="Calendar months">
                    <a href="<?php echo calendar_escape($calendarRouteBase . rawurlencode($calendar['previous'])); ?>" aria-label="Previous month"><i class="fa fa-chevron-left"></i></a>
                    <a class="is-today" href="<?php echo calendar_escape($calendarRouteBase . date('Y-m')); ?>">Today</a>
                    <a href="<?php echo calendar_escape($calendarRouteBase . rawurlencode($calendar['next'])); ?>" aria-label="Next month"><i class="fa fa-chevron-right"></i></a>
                </nav>
            </div>
            <div class="calendar-weekdays" aria-hidden="true"><?php foreach (array('Mon','Tue','Wed','Thu','Fri','Sat','Sun') as $weekday): ?><span><?php echo $weekday; ?></span><?php endforeach; ?></div>
            <div class="calendar-grid">
                <?php for ($blank = 0; $blank < $startOffset; $blank++): ?><div class="calendar-day is-outside" aria-hidden="true"></div><?php endfor; ?>
                <?php for ($day = 1; $day <= $daysInMonth; $day++): $date = $calendar['month'] . '-' . str_pad($day, 2, '0', STR_PAD_LEFT); $dayEvents = $eventsByDate[$date] ?? array(); ?>
                    <article class="calendar-day<?php echo $date === $today ? ' is-today' : ''; ?><?php echo $dayEvents ? ' has-events' : ''; ?>" aria-label="<?php echo calendar_escape(date('l, j F Y', strtotime($date))); ?>">
                        <span class="calendar-day-number"><?php echo $day; ?></span>
                        <div class="calendar-day-events">
                            <?php foreach (array_slice($dayEvents, 0, 3) as $event): ?>
                                <div class="calendar-event is-<?php echo calendar_escape($event['type']); ?>" title="<?php echo calendar_escape($event['description']); ?>">
                                    <span><?php echo $event['start_time'] !== '00:00' ? calendar_escape(date('g:i a', strtotime($event['start_time']))) : 'All day'; ?></span>
                                    <strong><?php echo calendar_escape($event['title']); ?></strong>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($dayEvents) > 3): ?><small>+<?php echo count($dayEvents) - 3; ?> more</small><?php endif; ?>
                        </div>
                    </article>
                <?php endfor; ?>
            </div>
        </section>

        <aside class="calendar-side">
            <?php if ($canManage): ?>
                <section class="calendar-card calendar-manager">
                    <div class="calendar-card-heading"><span class="calendar-heading-icon"><i class="fa fa-plus"></i></span><div><small><?php echo $isAdminCalendar ? 'School schedule' : 'Assigned classes'; ?></small><h2><?php echo $isAdminCalendar ? 'Add academic event' : 'Add class session'; ?></h2></div></div>
                    <?php if (!$isAdminCalendar && empty($calendarContext['classes'])): ?>
                        <div class="calendar-empty-small">No class-teacher allocation is active for your account this term.</div>
                    <?php else: ?>
                    <form method="post" action="<?php echo calendar_escape($calendarContext['form_action']); ?>" class="calendar-form">
                        <input type="hidden" name="csrf_token" value="<?php echo calendar_escape($calendarContext['csrf']); ?>">
                        <input type="hidden" name="schedule_action" value="create">
                        <label><span><?php echo $isAdminCalendar ? 'Event title' : 'Class / lesson title'; ?></span><input type="text" name="title" maxlength="300" required placeholder="<?php echo $isAdminCalendar ? 'e.g. Mid-term break' : 'e.g. Mathematics revision'; ?>"></label>
                        <label><span><?php echo $isAdminCalendar ? 'Audience' : 'Assigned class'; ?></span><select name="class_id" required><?php if ($isAdminCalendar): ?><option value="ALL">All learners</option><?php endif; ?><?php foreach ($calendarContext['classes'] as $class): ?><option value="<?php echo calendar_escape($class['classid']); ?>"><?php echo calendar_escape($class['classname']); ?></option><?php endforeach; ?></select></label>
                        <?php if ($isAdminCalendar): ?><label><span>Academic term</span><input type="text" name="term" maxlength="64" value="<?php echo calendar_escape($calendarContext['term']); ?>" required></label><?php endif; ?>
                        <div class="calendar-form-row"><label><span><?php echo $isAdminCalendar ? 'Start date' : 'First class date'; ?></span><input type="date" name="start_date" value="<?php echo calendar_escape(date('Y-m-d')); ?>" required></label><label data-calendar-end-date><span>End date</span><input type="date" name="end_date" value="<?php echo calendar_escape($isAdminCalendar ? date('Y-m-d') : date('Y-m-d', strtotime('+12 weeks'))); ?>"></label></div>
                        <div class="calendar-form-row"><label><span>Starts</span><input type="time" name="start_time" value="<?php echo $isAdminCalendar ? '08:00' : '09:00'; ?>" required></label><label><span>Ends</span><input type="time" name="end_time" value="<?php echo $isAdminCalendar ? '09:00' : '10:00'; ?>" required></label></div>
                        <?php if (!$isAdminCalendar): ?><label class="calendar-check"><input type="checkbox" name="repeat_weekly" value="1" checked data-calendar-repeat><span><strong>Repeat every week</strong><small>Uses the weekday of the first class date until the end date.</small></span></label><?php endif; ?>
                        <label><span>Details <small>(optional)</small></span><textarea name="description" maxlength="3000" rows="2" placeholder="Add a short note or location"></textarea></label>
                        <button type="submit"><i class="fa fa-calendar-plus-o"></i> <?php echo $isAdminCalendar ? 'Publish event' : 'Add to timetable'; ?></button>
                    </form>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section class="calendar-card calendar-agenda">
                <div class="calendar-card-heading"><span class="calendar-heading-icon is-blue"><i class="fa fa-list-ul"></i></span><div><small>At a glance</small><h2><?php echo calendar_escape($calendar['label']); ?> agenda</h2></div></div>
                <div class="calendar-agenda-list">
                    <?php foreach (array_slice(array_values($uniqueEvents), 0, 8) as $event): ?>
                        <article><time datetime="<?php echo calendar_escape($event['date']); ?>"><strong><?php echo date('d', strtotime($event['date'])); ?></strong><span><?php echo date('M', strtotime($event['date'])); ?></span></time><div><span class="calendar-type is-<?php echo calendar_escape($event['type']); ?>"><?php echo $event['type'] === 'academic' ? 'School event' : 'Class'; ?></span><h3><?php echo calendar_escape($event['title']); ?></h3><p><?php echo calendar_escape($event['start_time'] . '–' . $event['end_time'] . ' · ' . $event['class_name']); ?></p></div>
                        <?php if ($canManage && ($isAdminCalendar || $event['owner'] === $calendarContext['actor_id'])): ?><form method="post" action="<?php echo calendar_escape($calendarContext['form_action']); ?>" onsubmit="return confirm('Remove this calendar entry?');"><input type="hidden" name="csrf_token" value="<?php echo calendar_escape($calendarContext['csrf']); ?>"><input type="hidden" name="schedule_action" value="delete"><input type="hidden" name="event_id" value="<?php echo (int) $event['id']; ?>"><button type="submit" aria-label="Remove <?php echo calendar_escape($event['title']); ?>"><i class="fa fa-trash-o"></i></button></form><?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                    <?php if (!$uniqueEvents): ?><div class="calendar-empty-small"><i class="fa fa-calendar-o"></i><strong>No activities yet</strong><span>Published events and class sessions will appear here.</span></div><?php endif; ?>
                </div>
            </section>
        </aside>
    </div>

    <section class="calendar-card timetable-card" aria-labelledby="timetable-title">
        <div class="calendar-card-heading"><span class="calendar-heading-icon is-gold"><i class="fa fa-clock-o"></i></span><div><small>Repeats weekly</small><h2 id="timetable-title">Class timetable</h2></div></div>
        <?php if (!empty($calendarContext['timetable'])): ?>
            <div class="timetable-scroll"><table><thead><tr><th>Day</th><th>Time</th><th>Class / lesson</th><th>Class</th><th>Period</th></tr></thead><tbody><?php foreach ($calendarContext['timetable'] as $session): ?><tr><td><strong><?php echo calendar_escape(date('l', strtotime($session['schdate']))); ?></strong></td><td><?php echo calendar_escape(date('g:i a', strtotime($session['start_time'])) . ' – ' . date('g:i a', strtotime($session['stop_time']))); ?></td><td><strong><?php echo calendar_escape($session['title']); ?></strong><small><?php echo calendar_escape($session['description']); ?></small></td><td><?php echo calendar_escape($session['classname']); ?></td><td><?php echo calendar_escape(date('j M', strtotime($session['schdate'])) . ' – ' . date('j M Y', strtotime($session['stp_date']))); ?></td></tr><?php endforeach; ?></tbody></table></div>
        <?php else: ?><div class="calendar-empty-timetable"><i class="fa fa-clock-o"></i><div><strong>No recurring class sessions yet</strong><span>A class teacher can add weekly sessions once a class has been assigned.</span></div></div><?php endif; ?>
    </section>
</div>
