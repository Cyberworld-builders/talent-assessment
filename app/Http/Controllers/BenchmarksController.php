<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Benchmark;
use App\Dimension;
use App\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BenchmarksController extends Controller
{
    /**
     * Show the assessment selection page for benchmarks.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectAssessment()
    {
        $assessments = Assessment::orderBy('name')->get();
        
        return view('dashboard.benchmarks.select-assessment', compact('assessments'));
    }

    /**
     * Show the industry selection page for benchmarks.
     *
     * @param int $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function selectIndustry($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $industries = Industry::orderBy('name')->get();
        
        return view('dashboard.benchmarks.select-industry', compact('assessment', 'industries'));
    }

    /**
     * Display the benchmarks for a specific assessment and industry.
     *
     * @param int $assessmentId
     * @param int $industryId
     * @return \Illuminate\Http\Response
     */
    public function index($assessmentId, $industryId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $industry = Industry::findOrFail($industryId);
        $dimensions = $assessment->dimensions()->orderBy('name')->get();
        
        // Get existing benchmarks for this assessment and industry
        $benchmarks = Benchmark::where('industry_id', $industryId)
            ->whereHas('dimension', function ($query) use ($assessmentId) {
                $query->where('assessment_id', $assessmentId);
            })
            ->get()
            ->keyBy('dimension_id');
        
        return view('dashboard.benchmarks.index', compact('assessment', 'industry', 'dimensions', 'benchmarks'));
    }

    /**
     * Store benchmark data.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|exists:assessments,id',
            'industry_id' => 'required|exists:industries,id',
            'benchmarks' => 'required|array',
            'benchmarks.*.dimension_id' => 'required|exists:dimensions,id',
            'benchmarks.*.value' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $assessmentId = $request->input('assessment_id');
        $industryId = $request->input('industry_id');
        $benchmarks = $request->input('benchmarks');

        foreach ($benchmarks as $benchmarkData) {
            $dimensionId = $benchmarkData['dimension_id'];
            $value = $benchmarkData['value'];

            // Check if dimension belongs to the assessment
            $dimension = Dimension::where('id', $dimensionId)
                ->where('assessment_id', $assessmentId)
                ->first();

            if (!$dimension) {
                continue; // Skip if dimension doesn't belong to assessment
            }

            // Update or create benchmark
            Benchmark::updateOrCreate(
                [
                    'dimension_id' => $dimensionId,
                    'industry_id' => $industryId
                ],
                [
                    'value' => $value
                ]
            );
        }

        return redirect()->back()
            ->with('success', 'Benchmarks saved successfully!');
    }

    /**
     * Handle Excel file upload for bulk benchmark data.
     *
     * @param Request $request
     * @param int $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, $assessmentId)
    {
        // Suppress deprecation warnings for PHPExcel
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        
        $validator = Validator::make($request->all(), [
            'industry_id' => 'required|exists:industries,id',
            'excel_file' => 'required|file|mimes:xls,xlsx|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $industryId = $request->input('industry_id');
        $assessment = Assessment::findOrFail($assessmentId);
        $industry = Industry::findOrFail($industryId);

        try {
            $file = $request->file('excel_file');
            
            // Read Excel file
            $data = Excel::load($file->getPathname(), function($reader) {
                $reader->noHeading();
            })->get();

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($data as $row) {
                if (count($row) < 2) {
                    $errorCount++;
                    continue;
                }

                $dimensionName = trim($row[0]);
                $value = trim($row[1]);

                if (empty($dimensionName) || empty($value)) {
                    $errorCount++;
                    continue;
                }

                // Find dimension by name for this assessment
                $dimension = Dimension::where('name', $dimensionName)
                    ->where('assessment_id', $assessmentId)
                    ->first();

                if (!$dimension) {
                    $errors[] = "Dimension '$dimensionName' not found for assessment '{$assessment->name}'";
                    $errorCount++;
                    continue;
                }

                // Create or update benchmark
                Benchmark::updateOrCreate(
                    [
                        'dimension_id' => $dimension->id,
                        'industry_id' => $industryId
                    ],
                    [
                        'value' => $value
                    ]
                );

                $successCount++;
            }

            $message = "Upload completed: $successCount benchmarks processed successfully.";
            if ($errorCount > 0) {
                $message .= " $errorCount rows had errors.";
            }

            return redirect()->back()
                ->with('success', $message)
                ->with('upload_errors', $errors);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Download benchmark template Excel file.
     *
     * @param int $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate($assessmentId)
    {
        // Suppress deprecation warnings for PHPExcel
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        
        $assessment = Assessment::findOrFail($assessmentId);
        $dimensions = $assessment->dimensions()->orderBy('name')->get();

        $data = [];
        foreach ($dimensions as $dimension) {
            $data[] = [
                'Dimension Name' => $dimension->name,
                'Benchmark Value' => ''
            ];
        }

        Excel::create("benchmarks_template_{$assessment->name}", function($excel) use ($data) {
            $excel->sheet('Benchmarks', function($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download('xlsx');
    }
}
