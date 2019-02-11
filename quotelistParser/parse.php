<?php
const IN = "quotelist";
const OUT = "out/";

$tops = preg_split('/\n*={10,}/', file_get_contents(IN));
foreach ($tops as $i => $top) {
    if ($i < 3) continue; // Skip intro, Top 0 and Top 1

    $subs = preg_split('/\n*-{10,}/', $top);
    foreach($subs as $j => $sub) {
        /*Numbering: ttsnnnn
          tt = capter number ("Top tt"), starting at 10
          s = singleline-quote (0 = single, 1 = multi)
          nnnn = unique number within the section, incremential, starting at 0001*/
        $startId = ((7 + $i) * 10 + $j) * 10000 + 1;

        if ($j === 0)
            ;//TODO awk script for multiline-quote
        else
            shell_exec('echo "'.$sub.'" | awk -v i='.$startId.' -f single.awk >> '.OUT.'/single');
    }
}
?>
