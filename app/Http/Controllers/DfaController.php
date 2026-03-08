<?php

namespace App\Http\Controllers;

use App\Models\Dfa\NumberStateMachine;

class DfaController extends Controller
{
    public function solve()
    {
        $stateMachine = new NumberStateMachine();

        $tests = [
            '0',
            '5',
            '12',
            '-3',
            '-45',
            '0.2',
            '12.34',
            '-0.75',
            '00',
            '012',
            '.5',
            '1.',
            '-',
            '--2',
            'abc',
        ];
        $response = [];

        foreach ($tests as $test) {
            $result = $stateMachine->validate($test);
            $response[$test] = $result;
        }

        return view('dfa', ['response' => $response]);
    }
}
