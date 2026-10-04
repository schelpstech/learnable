<?php

// Explicit endpoint allowlists protect both routed pages and legacy direct URLs.
return [
    'registry' => [
        'dashboard.php', 'registry_dashboard.php', 'cclass.php', 'createclass.php', 'mgclass.php',
        'csubj.php', 'createsbj.php', 'delsubject.php', 'allocate.php', 'doallocate.php',
        'allocateclass.php', 'allocatedit.php', 'edallocated.php', 'delalloc.php', 'deltutor.php', 'tutallocated.php',
        'mglearners.php', 'createle.php', 'edlearner.php', 'stdedit.php', 'stdact.php',
        'mgterm.php', 'modifyTerm.php', 'configterm.php', 'calendar.php', 'promote.php', 'promoteall.php',
        'scorebook.php', 'mgresult.php', 'mgaffective.php', 'mgmid.php', 'mgreport.php',
        'mgconfig.php', 'configres.php', 'chgstatus.php', 'resultstatus.php', 'delrecord.php', 'delrating.php',
        'delresult.php', 'upresult.php', 'viewresult.php', 'viewcumresult.php', 'viewmidterm.php',
        'viewmultiresult.php', 'viewmulticum.php', 'report_class_common.php', 'report_view_common.php',
        'get_std.php', 'get_state.php', 'mdgetstd.php', 'chart.php', 'popca.php', 'table.php',
        'template.php', 'multitemplate.php', 'headerresult.php', 'footerrsult.php',
        'cbt.php', 'mglesson.php', 'filedash.php', 'edmaterial.php', 'lsnedit.php', 'lsndel.php',
    ],
    'bursary' => [
        'mgfee.php', 'createfee.php', 'edfee.php', 'feedit.php', 'deactfee.php', 'activatefee.php', 'delfee.php',
        'assignfee.php', 'feeassign.php', 'fee_learners.php', 'mgdiscount.php', 'discassign.php',
        'expenses.php', 'inventory.php', 'payrecord.php', 'payreport.php', 'payview.php', 'getclassrecord.php',
        'recordpayment.php', 'modifyPayRecord.php', 'receipt.php', 'get_fee.php', 'getdisc.php', 'getamt.php',
    ],
];
