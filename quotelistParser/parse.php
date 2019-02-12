<?php

const IN = "quotelist";
const OUT = "out/";

/**
 * Numbering: ttsnnnn
 * tt = capter number ("Top tt"), starting at 10
 * s = singleline-quote (0 = single, 1 = multi)
 * nnnn = unique number within the section, incremential, starting at 0001
 */

$tops = preg_split('/\n*={10,}/', file_get_contents(IN)); // Split file into different Tops
$file = fopen(OUT."single", "w+") or die("Can't write single-output file!");
fclose($file);
$file = fopen(OUT."multi", "w+") or die("Can't write multi-output file!");
foreach ($tops as $i => $top) {
    if ($i < 3) continue; // Skip intro, Top 0 and Top 1

    $subs = preg_split('/\n*-{10,}/', $top); // Split Top into the multi and single segment

    // Single-line-quotes
    $startId = ((7 + $i) * 10 + 1) * 10000 + 1;
    $out = OUT."single";
    shell_exec("echo \"$subs[1]\" | awk -v i=$startId -f single.awk >> $out");

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
        fwrite($file, $output);
        $startId++;
    }
}
fclose($file);
