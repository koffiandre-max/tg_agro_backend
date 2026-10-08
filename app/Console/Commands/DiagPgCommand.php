<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

Artisan::command('pg:activity', function () {
    $rows = DB::select("select pid, state, wait_event_type, wait_event, now()-xact_start as dur, left(query, 100) as q
        from pg_stat_activity
        where datname = current_database() and pid <> pg_backend_pid()
        order by xact_start nulls last");

    $this->info('CONNEXIONS ACTIVES: ' . count($rows));
    foreach ($rows as $r) {
        $this->line("pid={$r->pid} state={$r->state} wait={$r->wait_event_type}/{$r->wait_event} dur={$r->dur} q={$r->q}");
    }

    $locks = DB::select("select count(*) as n from pg_locks where not granted");
    $this->info('VERROUS EN ATTENTE: ' . $locks[0]->n);

    return 0;
});
