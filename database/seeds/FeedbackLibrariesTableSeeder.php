<?php

use Illuminate\Database\Seeder;
use App\FeedbackLibrary;

class FeedbackLibrariesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a general feedback library
        FeedbackLibrary::create([
            'name' => 'General Assessment Feedback',
            'client_id' => null,
            'feedback' => [
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
                    ],
                    'teamwork' => [
                        'high' => 'Excellent teamwork skills. You collaborate effectively and contribute positively to team dynamics. Consider taking on team leadership roles and mentoring others.',
                        'medium' => 'Good teamwork abilities. Continue developing collaboration skills and active participation in group activities.',
                        'low' => 'Teamwork development needed. Focus on building relationships with colleagues and contributing actively to group projects.'
                    ],
                    'adaptability' => [
                        'high' => 'Outstanding adaptability. You handle change well and remain flexible in dynamic environments. Continue embracing new challenges and opportunities.',
                        'medium' => 'Good adaptability skills. Continue developing flexibility and openness to change in your work environment.',
                        'low' => 'Adaptability development needed. Focus on building resilience and openness to change. Practice handling unexpected situations.'
                    ]
                ]
            ]
        ]);

        // Create a technology industry specific library
        FeedbackLibrary::create([
            'name' => 'Technology Industry Assessment Feedback',
            'client_id' => null,
            'feedback' => [
                'dimensions' => [
                    'technical_leadership' => [
                        'high' => 'Exceptional technical leadership demonstrated. You excel at guiding technical teams and making architectural decisions. Consider advancing to senior technical roles.',
                        'medium' => 'Good technical leadership potential. Focus on developing your technical decision-making skills and team guidance abilities.',
                        'low' => 'Technical leadership development needed. Start by building technical expertise and confidence in leading technical discussions.'
                    ],
                    'innovation' => [
                        'high' => 'Outstanding innovation capabilities. You consistently generate creative solutions and drive technological advancement. Continue pushing boundaries and sharing ideas.',
                        'medium' => 'Good innovation potential. Continue developing creative thinking and exploring new technologies and approaches.',
                        'low' => 'Innovation development needed. Focus on exploring new technologies and practicing creative problem-solving approaches.'
                    ],
                    'agile_methodology' => [
                        'high' => 'Excellent agile methodology skills. You effectively lead agile teams and drive continuous improvement. Consider mentoring others in agile practices.',
                        'medium' => 'Solid agile methodology abilities. Continue developing your agile practices and team facilitation skills.',
                        'low' => 'Agile methodology development needed. Focus on understanding agile principles and practices through training and hands-on experience.'
                    ],
                    'code_quality' => [
                        'high' => 'Exceptional code quality standards. You write clean, maintainable code and set high standards for the team. Continue mentoring others in best practices.',
                        'medium' => 'Good code quality practices. Continue developing your coding standards and code review skills.',
                        'low' => 'Code quality development needed. Focus on learning best practices, code review processes, and writing cleaner, more maintainable code.'
                    ]
                ]
            ]
        ]);

        // Create a healthcare industry specific library
        FeedbackLibrary::create([
            'name' => 'Healthcare Industry Assessment Feedback',
            'client_id' => null,
            'feedback' => [
                'dimensions' => [
                    'patient_care' => [
                        'high' => 'Exceptional patient care skills. You demonstrate outstanding empathy and clinical excellence. Consider mentoring others and advancing to leadership roles.',
                        'medium' => 'Good patient care abilities. Continue developing your clinical skills and patient interaction techniques.',
                        'low' => 'Patient care development needed. Focus on building clinical skills and improving patient communication and empathy.'
                    ],
                    'clinical_judgment' => [
                        'high' => 'Outstanding clinical judgment. You make sound decisions under pressure and demonstrate excellent diagnostic skills. Continue advancing your clinical expertise.',
                        'medium' => 'Solid clinical judgment. Continue developing your diagnostic skills and decision-making abilities in complex situations.',
                        'low' => 'Clinical judgment development needed. Focus on building diagnostic skills and improving decision-making through additional training and experience.'
                    ],
                    'team_collaboration' => [
                        'high' => 'Excellent healthcare team collaboration. You work effectively with multidisciplinary teams and contribute to positive patient outcomes.',
                        'medium' => 'Good team collaboration skills. Continue developing your ability to work with diverse healthcare professionals.',
                        'low' => 'Team collaboration development needed. Focus on building relationships with colleagues and improving communication within healthcare teams.'
                    ],
                    'safety_consciousness' => [
                        'high' => 'Outstanding safety consciousness. You consistently prioritize patient safety and maintain high safety standards. Consider leading safety initiatives.',
                        'medium' => 'Good safety awareness. Continue developing your understanding of safety protocols and best practices.',
                        'low' => 'Safety consciousness development needed. Focus on learning safety protocols and developing a stronger safety mindset in patient care.'
                    ]
                ]
            ]
        ]);
    }
}