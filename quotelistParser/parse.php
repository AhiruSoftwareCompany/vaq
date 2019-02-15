<?php

$in = "quotelist";
$outDir = "out";
$out = $outDir."/quotes";

/**
 * Numbering: ttsnnnn
 * tt = chapter number ("Top tt"), starting at 10
 * s = singleline-quote (0 = single, 1 = multi)
 * nnnn = unique number within the section, incremential, starting at 0001
 */

if (!file_exists($outDir)) mkdir($outDir, 0777, true);
$file = fopen($out, "w+") or die("Can't write output file!\n");
fclose($file);
if (!file_exists($in)) die("Can't read input file!\n");
$tops = preg_split('/\n*={10,}/', file_get_contents($in)); // Split file into different Tops

foreach ($tops as $i => $top) {
    if ($i < 3) continue; // Skip intro, Top 0 and Top 1

    $subs = preg_split('/\n*-{10,}/', $top); // Split Top into the multi and single segment

    // Multi-line-quotes
    $startId = (7 + $i) * 100000 + 1;
    $quotes = explode(PHP_EOL.PHP_EOL, $subs[0]);
    foreach($quotes as $k => $quote) {
        if ($k < 1) continue; // Skip headline of Top
        $output = "id: $startId\n";
        $lines = explode(PHP_EOL, $quote); // Split quote into separate lines

        foreach($lines as $l => $line) {
            $tabs = preg_split('/\t+/', $line); // Split line in two halfes
            if ($l === 0) {
                if (count($tabs) === 1) // Check if there is a prelude
                    $output .= "date: $line\n\n";
                else
                    $output .= "date: $tabs[0]\n\n$tabs[1]\n";
            } else {
                $output .= "$tabs[0]: $tabs[1]\n";
            }
        }

        $output .= "---\n";
        file_put_contents($out, $output, FILE_APPEND);
        $startId++;
    }

    // Single-line-quotes
    $startId = ((7 + $i) * 10 + 1) * 10000 + 1;
    shell_exec("echo \"$subs[1]\" | awk -v i=$startId -f single.awk >> $out");
}
