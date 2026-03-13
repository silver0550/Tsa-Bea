<?php

namespace App\Http\Controllers;

use App\Models\Dfa\NumberStateMachine;
use App\Services\Ll1ExpressionParserService;
use Illuminate\Http\Request;
use Illuminate\View\View;


class DfaController extends Controller
{
    public function solve(): View
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

    public function ll1Index(): View
    {
        return view('ll1Parser');
    }

    public function evaluate(Request $request)
    {
        $expression = $request->input('expression');

        try {
            $parser = resolve(Ll1ExpressionParserService::class);
            $result = $parser->parse($expression);

            return view('ll1Parser', [
                'expression' => $expression,
                'value' => $result['value'],
                'productions' => $result['productions']
            ]);
        } catch (\Throwable $e) {

            return view('ll1Parser', [
                'expression' => $expression,
                'error' => $e->getMessage()
            ]);
        }
    }
}
