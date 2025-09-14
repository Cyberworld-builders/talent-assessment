<?php

namespace App\Services;

use App\FeedbackLibrary;
use App\User;
use App\Assessment;
use App\Assignment;
use Illuminate\Support\Facades\Cache;

class FeedbackService
{
    /**
     * Generate personalized feedback for a user based on assessment scores.
     *
     * @param User $user
     * @param Assessment $assessment
     * @param array $scores
     * @return array
     */
    public function generateFeedback(User $user, Assessment $assessment, $scores)
    {
        $library = $this->getBestFeedbackLibrary($user, $assessment);
        
        if (!$library) {
            return $this->generateDefaultFeedback($scores);
        }
        
        return $this->buildPersonalizedFeedback($scores, $library);
    }

    /**
     * Generate feedback for an assignment.
     *
     * @param Assignment $assignment
     * @param array $scores
     * @return array
     */
    public function generateFeedbackForAssignment(Assignment $assignment, $scores)
    {
        $user = $assignment->user;
        $assessment = $assignment->assessment();
        
        return $this->generateFeedback($user, $assessment, $scores);
    }

    /**
     * Get the best feedback library for a user and assessment.
     *
     * @param User $user
     * @param Assessment $assessment
     * @return FeedbackLibrary|null
     */
    private function getBestFeedbackLibrary(User $user, Assessment $assessment)
    {
        $cacheKey = "feedback_library_{$user->client_id}_{$assessment->id}";
        
        return Cache::remember($cacheKey, 3600, function() use ($user, $assessment) {
            // Check for client-specific library first
            if ($user->client_id) {
                $clientLibrary = FeedbackLibrary::where('client_id', $user->client_id)->first();
                if ($clientLibrary) {
                    return $clientLibrary;
                }
            }
            
            // Check for industry-specific library
            if ($user->industry_id) {
                $industryLibrary = FeedbackLibrary::where('name', 'like', '%' . $user->industry->name . '%')
                    ->where('client_id', null)
                    ->first();
                if ($industryLibrary) {
                    return $industryLibrary;
                }
            }
            
            // Fall back to global library
            return FeedbackLibrary::where('name', 'General Assessment Feedback')
                ->where('client_id', null)
                ->first();
        });
    }

    /**
     * Build personalized feedback from scores and library.
     *
     * @param array $scores
     * @param FeedbackLibrary $library
     * @return array
     */
    private function buildPersonalizedFeedback($scores, FeedbackLibrary $library)
    {
        $feedback = [];
        $feedbackData = $library->feedback;
        
        foreach ($scores as $dimension => $score) {
            if (isset($feedbackData['dimensions'][$dimension])) {
                $level = $this->getPerformanceLevel($score);
                $feedback[$dimension] = [
                    'score' => $score,
                    'level' => $level,
                    'feedback' => $feedbackData['dimensions'][$dimension][$level] ?? '',
                    'color' => $this->getLevelColor($level),
                    'icon' => $this->getLevelIcon($level),
                    'action_items' => $this->generateActionItems($level, $dimension)
                ];
            }
        }
        
        return $feedback;
    }

    /**
     * Determine performance level based on score.
     *
     * @param int $score
     * @return string
     */
    private function getPerformanceLevel($score)
    {
        if ($score >= 80) return 'high';
        if ($score >= 60) return 'medium';
        return 'low';
    }

    /**
     * Get color class for performance level.
     *
     * @param string $level
     * @return string
     */
    private function getLevelColor($level)
    {
        switch ($level) {
            case 'high': return 'success';
            case 'medium': return 'warning';
            case 'low': return 'danger';
            default: return 'info';
        }
    }

    /**
     * Get icon class for performance level.
     *
     * @param string $level
     * @return string
     */
    private function getLevelIcon($level)
    {
        switch ($level) {
            case 'high': return 'fa-star';
            case 'medium': return 'fa-star-half-o';
            case 'low': return 'fa-star-o';
            default: return 'fa-question';
        }
    }

    /**
     * Generate action items based on performance level and dimension.
     *
     * @param string $level
     * @param string $dimension
     * @return array
     */
    private function generateActionItems($level, $dimension)
    {
        $actions = [];
        
        switch ($level) {
            case 'high':
                $actions = [
                    'Continue developing advanced skills in this area',
                    'Mentor others who are developing in this dimension',
                    'Take on leadership opportunities that utilize these strengths'
                ];
                break;
            case 'medium':
                $actions = [
                    'Practice and refine current skills regularly',
                    'Seek feedback from experts in this area',
                    'Take on challenging projects to build experience'
                ];
                break;
            case 'low':
                $actions = [
                    'Focus on fundamental development in this area',
                    'Seek training and educational resources',
                    'Practice consistently to build confidence'
                ];
                break;
        }
        
        // Add dimension-specific actions
        $dimensionActions = $this->getDimensionSpecificActions($dimension, $level);
        $actions = array_merge($actions, $dimensionActions);
        
        return $actions;
    }

    /**
     * Get dimension-specific action items.
     *
     * @param string $dimension
     * @param string $level
     * @return array
     */
    private function getDimensionSpecificActions($dimension, $level)
    {
        $actions = [];
        
        switch (strtolower($dimension)) {
            case 'leadership':
                if ($level === 'high') {
                    $actions[] = 'Consider executive leadership programs';
                } elseif ($level === 'medium') {
                    $actions[] = 'Practice leading small team projects';
                } else {
                    $actions[] = 'Start with informal leadership opportunities';
                }
                break;
                
            case 'communication':
                if ($level === 'high') {
                    $actions[] = 'Mentor others in communication skills';
                } elseif ($level === 'medium') {
                    $actions[] = 'Join public speaking groups';
                } else {
                    $actions[] = 'Practice active listening techniques';
                }
                break;
                
            case 'problem_solving':
                if ($level === 'high') {
                    $actions[] = 'Tackle complex strategic challenges';
                } elseif ($level === 'medium') {
                    $actions[] = 'Practice structured problem-solving methods';
                } else {
                    $actions[] = 'Start with simple problem-solving exercises';
                }
                break;
        }
        
        return $actions;
    }

    /**
     * Generate default feedback when no library is available.
     *
     * @param array $scores
     * @return array
     */
    private function generateDefaultFeedback($scores)
    {
        $feedback = [];
        
        foreach ($scores as $dimension => $score) {
            $level = $this->getPerformanceLevel($score);
            $feedback[$dimension] = [
                'score' => $score,
                'level' => $level,
                'feedback' => $this->getDefaultFeedbackMessage($level),
                'color' => $this->getLevelColor($level),
                'icon' => $this->getLevelIcon($level),
                'action_items' => $this->generateActionItems($level, $dimension)
            ];
        }
        
        return $feedback;
    }

    /**
     * Get default feedback message for performance level.
     *
     * @param string $level
     * @return string
     */
    private function getDefaultFeedbackMessage($level)
    {
        switch ($level) {
            case 'high':
                return 'Excellent performance in this area. Continue building on your strengths and consider mentoring others.';
            case 'medium':
                return 'Good performance with room for improvement. Focus on development areas and seek additional practice opportunities.';
            case 'low':
                return 'Development opportunity identified. Consider additional training and practice to strengthen this area.';
            default:
                return 'Performance level not determined. Please review your assessment results.';
        }
    }

    /**
     * Get all available feedback libraries for a client.
     *
     * @param int|null $clientId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLibrariesForClient($clientId = null)
    {
        $query = FeedbackLibrary::with('client');
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        } else {
            $query->where('client_id', null);
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * Create a default feedback library for a client.
     *
     * @param int $clientId
     * @param string $clientName
     * @return FeedbackLibrary
     */
    public function createDefaultLibrary($clientId, $clientName)
    {
        $defaultFeedback = [
            'dimensions' => [
                'leadership' => [
                    'high' => 'Exceptional leadership capabilities demonstrated. Your ability to inspire and guide teams is outstanding. Consider mentoring others and taking on strategic leadership roles.',
                    'medium' => 'Good leadership foundation. Focus on developing your influence skills and decision-making confidence. Practice leading small projects to build experience.',
                    'low' => 'Leadership development opportunity identified. Start by building confidence in group settings and practicing clear communication. Consider leadership training programs.'
                ],
                'communication' => [
                    'high' => 'Outstanding communication skills. You effectively convey ideas and build rapport with others. Continue developing advanced communication techniques and consider mentoring others.',
                    'medium' => 'Good communication abilities. Continue practicing clear and concise expression. Seek opportunities to present and engage in group discussions.',
                    'low' => 'Communication skills need improvement. Focus on clarity and active listening. Practice expressing ideas clearly and seek feedback on your communication style.'
                ],
                'problem_solving' => [
                    'high' => 'Exceptional problem-solving abilities. You approach challenges systematically and creatively. Continue tackling complex problems and share your methods with others.',
                    'medium' => 'Solid problem-solving skills. Continue developing analytical thinking approaches. Practice breaking down complex issues into manageable parts.',
                    'low' => 'Problem-solving development needed. Focus on logical reasoning and structured problem-solving methods. Start with simple problems and gradually increase complexity.'
                ]
            ]
        ];

        return FeedbackLibrary::create([
            'name' => $clientName . ' Assessment Feedback',
            'client_id' => $clientId,
            'feedback' => $defaultFeedback
        ]);
    }
}
