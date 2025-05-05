<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommentsReport;
use App\Models\ReportStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of all the reports (story and comments).
     * 
     * This method fetches all story reports and all comment reports, along with their
     * associated story and comment respectively using eager loading with 'with()'.
     * It then returns a JSON response containing both sets of reports.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response with all of the data.
     */
    public function index() : JsonResponse
    {
        $storyReports = ReportStory::with('story')->get();
        $commentReports = CommentsReport::with('comment')->get();

        return response()->json([
            'reports_stories' => $storyReports,
            'comment_reports' => $commentReports,
        ]);
    }

    /**
     * Display the specified report (either story or comment)
     * 
     * This method attempts to find a report by its ID in both the ReportStory and
     * CommentsReport models. If it finds a report, it returns it along with a type identifier (either
     * 'story' or 'comment'). If no report is found, it returns a 404 error.
     * 
     * @param int $id
     * Could be the ID of either ReportStory or CommentsReport.
     * 
     * @return \Illuminate\Http\JsonResponse
     * It returns either a report of a story, comment or a 404 error.
     */
    public function show($id) : JsonResponse
    {
        $storyReport = ReportStory::with('story')->find($id);
        $commentReport = CommentsReport::with('comment')->find($id);

        if ($storyReport) {
            return response()->json(['report' => $storyReport, 'type' => 'story']);
        }

        if ($commentReport) {
            return response()->json(['report' => $commentReport, 'type' => 'comment']);
        }

        return response()->json(['message' => 'Report not found'], 404);
    }

    /**
     * TODO: Add the logic to update story and comment reports,
     * like marking as resolved.
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Handles deleting a report
     * 
     * This method finds the report by its ID and deletes it
     * from the corresponding table.
     * 
     * @param int $id
     * It's either the ID of ReportStory or CommentsReport.
     * 
     * @return \Illuminate\Http\JsonResponse
     * It returns either a success message from deleting a report or an error 404 code.
     */
    public function destroy($id) : JsonResponse
    {
        $storyReport = ReportStory::find($id);
        $commentReport = CommentsReport::find($id);

        if ($storyReport) {
            $storyReport->delete();
            return response()->json(['message' => 'Story report deleted']);
        }

        if ($commentReport) {
            $commentReport->delete();
            return response()->json(['message' => 'Comment report deleted']);
        }

        return response()->json(['message' => 'Report not found'], 404);
    }
}
