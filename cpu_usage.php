<?php

/**
 * základní fn pro získání informací o cpu
 *
 * @return array
 */
function GetCoreInformation(): array
{
    $cores = array();

    $cpu_data_fiile = file('/proc/stat');
    foreach ($cpu_data_fiile as $line) {
        if (preg_match('/^cpu[0-9]/', $line)) {
            $info = explode(' ', $line);
            $cores[] = array(
                'user' => $info[1],
                'nice' => $info[2],
                'sys' => $info[3],
                'idle' => $info[4]
            );
        }
    }
    return $cores;
}


/**
 * fn pro získání procentualních dat z cpu per core / ... 
 *
 * @param array $stat1
 * @param array $stat2
 * @return array
 */
function GetCpuPercentages(array $stat1, array $stat2): array
{
    $cpus = array();

    if (count($stat1) !== count($stat2)) {
        return [];
    }

    for ($i = 0, $l = count($stat1); $i < $l; $i++) {
        $dif = array();
        $dif['user'] = $stat2[$i]['user'] - $stat1[$i]['user'];
        $dif['nice'] = $stat2[$i]['nice'] - $stat1[$i]['nice'];
        $dif['sys'] = $stat2[$i]['sys'] - $stat1[$i]['sys'];
        $dif['idle'] = $stat2[$i]['idle'] - $stat1[$i]['idle'];
        $total = array_sum($dif);
        $cpu = array();
        foreach ($dif as $x => $y) $cpu[$x] = round($y / $total * 100, 1);
        $cpus['cpu' . $i] = $cpu;
    }
    return $cpus;
}


// získání prvního snapshotu 
$stat1 = GetCoreInformation();
// použití usleep pro rychlejsí odbavení
usleep(100000);
// získání druhého snapshotu 
$stat2 = GetCoreInformation();

$cpu_data = GetCpuPercentages($stat1, $stat2);


// výstup do fn();
// return $data;

// výstup do konzole
print_r($cpu_data);
