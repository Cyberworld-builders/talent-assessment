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
        // Clear existing feedback libraries
        FeedbackLibrary::truncate();

        // 1. General Assessment Feedback Library
        FeedbackLibrary::create([
            'name' => 'General Assessment Feedback',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'high' => 'Exceptional leadership capabilities demonstrated. Your ability to inspire and guide teams is outstanding. You show strong strategic thinking and decision-making skills. Consider mentoring others and taking on more senior leadership roles to further develop your capabilities.',
                        'medium' => 'Good leadership foundation with solid potential for growth. You demonstrate basic leadership skills and can work effectively in team settings. Focus on developing your influence skills, decision-making confidence, and ability to motivate others. Practice leading small projects to build experience.',
                        'low' => 'Leadership development opportunity identified. You have the potential to develop strong leadership skills with focused effort. Start by building confidence in group settings, practicing clear communication, and taking initiative in team activities. Consider leadership training programs and seek mentorship opportunities.'
                    ],
                    'communication' => [
                        'high' => 'Outstanding communication skills evident across all contexts. You excel at conveying complex ideas clearly, building rapport with diverse audiences, and adapting your communication style appropriately. Your written and verbal communication are both strong assets.',
                        'medium' => 'Good communication abilities with room for improvement. You can express ideas clearly in most situations and work well with others. Continue practicing clear and concise expression, active listening skills, and adapting your communication style to different audiences.',
                        'low' => 'Communication skills need improvement. Focus on developing clarity in your expression, active listening techniques, and confidence in presenting ideas. Practice in low-risk environments and consider communication skills training to build your capabilities.'
                    ],
                    'problem_solving' => [
                        'high' => 'Exceptional problem-solving abilities demonstrated. You approach challenges systematically and creatively, considering multiple perspectives and developing innovative solutions. Your analytical thinking and strategic approach to complex problems are outstanding.',
                        'medium' => 'Solid problem-solving skills with good analytical thinking. You can work through most challenges effectively and develop reasonable solutions. Continue developing systematic approaches to problem-solving and practice breaking down complex issues.',
                        'low' => 'Problem-solving development needed. Focus on developing structured approaches to challenges and analytical thinking skills. Practice breaking down complex issues into manageable parts and consider problem-solving methodologies training.'
                    ],
                    'teamwork' => [
                        'high' => 'Excellent teamwork and collaboration skills. You work effectively with diverse teams, contribute meaningfully to group goals, and help create positive team dynamics. Your ability to support others and work toward common objectives is outstanding.',
                        'medium' => 'Good teamwork abilities with solid collaboration skills. You can work effectively in team settings and contribute to group objectives. Continue developing your ability to support team members and work toward shared goals.',
                        'low' => 'Teamwork skills development needed. Focus on building collaboration abilities, supporting team members, and working effectively toward common goals. Practice active participation in group activities and seek opportunities to contribute to team success.'
                    ],
                    'adaptability' => [
                        'high' => 'Strong adaptability and flexibility demonstrated. You handle change effectively, learn quickly from new situations, and adjust your approach as needed. Your ability to thrive in dynamic environments is excellent.',
                        'medium' => 'Good adaptability with reasonable flexibility. You can handle most changes and new situations effectively. Continue developing your ability to learn quickly and adjust to different circumstances.',
                        'low' => 'Adaptability development needed. Focus on building flexibility and the ability to handle change effectively. Practice embracing new situations and learning from different experiences.'
                    ]
                ]
            ]
        ]);

        // 2. Technology Industry Feedback Library
        FeedbackLibrary::create([
            'name' => 'Technology Industry Feedback',
            'feedback' => [
                'dimensions' => [
                    'technical_skills' => [
                        'high' => 'Outstanding technical capabilities demonstrated. Your programming skills, system design abilities, and technical problem-solving are excellent. You show strong aptitude for learning new technologies and applying them effectively. Consider mentoring junior developers and contributing to technical architecture decisions.',
                        'medium' => 'Solid technical foundation with good programming skills. You can handle most technical challenges and contribute effectively to development projects. Focus on expanding your technical knowledge, learning new frameworks, and improving your system design skills.',
                        'low' => 'Technical skills development needed. Focus on building core programming abilities, understanding fundamental concepts, and gaining hands-on experience with relevant technologies. Consider additional training and practice projects.'
                    ],
                    'innovation' => [
                        'high' => 'Exceptional innovative thinking and creativity. You consistently generate novel solutions and approaches to technical challenges. Your ability to think outside the box and propose innovative solutions is outstanding.',
                        'medium' => 'Good innovative thinking with creative problem-solving abilities. You can develop creative solutions and think beyond conventional approaches. Continue developing your creative thinking and exploring new solution approaches.',
                        'low' => 'Innovation development needed. Focus on building creative thinking skills and exploring alternative approaches to problems. Practice brainstorming and considering multiple solution paths.'
                    ],
                    'agile_methodology' => [
                        'high' => 'Excellent understanding and application of agile methodologies. You work effectively in agile environments, contribute to sprint planning, and adapt quickly to changing requirements. Your agile mindset and practices are outstanding.',
                        'medium' => 'Good understanding of agile principles with solid application in practice. You can work effectively in agile teams and contribute to iterative development processes. Continue developing your agile practices and mindset.',
                        'low' => 'Agile methodology development needed. Focus on understanding agile principles, participating effectively in sprint activities, and adapting to iterative development approaches.'
                    ],
                    'code_quality' => [
                        'high' => 'Exceptional code quality and best practices. Your code is well-structured, maintainable, and follows industry best practices. You consistently write clean, efficient, and well-documented code.',
                        'medium' => 'Good code quality with solid programming practices. Your code is generally well-structured and maintainable. Continue focusing on code organization, documentation, and following best practices.',
                        'low' => 'Code quality development needed. Focus on writing clean, well-structured code, following programming best practices, and improving code documentation and organization.'
                    ]
                ]
            ]
        ]);

        // 3. Healthcare Industry Feedback Library
        FeedbackLibrary::create([
            'name' => 'Healthcare Industry Feedback',
            'feedback' => [
                'dimensions' => [
                    'patient_care' => [
                        'high' => 'Exceptional patient care skills demonstrated. Your ability to provide compassionate, effective care while maintaining professional standards is outstanding. You show strong clinical judgment and patient advocacy skills.',
                        'medium' => 'Good patient care abilities with solid clinical skills. You provide effective care and maintain professional standards. Continue developing your clinical judgment and patient communication skills.',
                        'low' => 'Patient care development needed. Focus on building clinical skills, improving patient communication, and developing professional care practices.'
                    ],
                    'clinical_knowledge' => [
                        'high' => 'Outstanding clinical knowledge and expertise. Your understanding of medical concepts, procedures, and best practices is excellent. You demonstrate strong clinical reasoning and evidence-based practice.',
                        'medium' => 'Solid clinical knowledge with good understanding of medical concepts. You can apply clinical knowledge effectively in practice. Continue expanding your medical knowledge and staying current with best practices.',
                        'low' => 'Clinical knowledge development needed. Focus on building medical knowledge, understanding clinical concepts, and staying current with healthcare practices.'
                    ],
                    'interdisciplinary_collaboration' => [
                        'high' => 'Excellent interdisciplinary collaboration skills. You work effectively with diverse healthcare teams, communicate clearly across disciplines, and contribute to coordinated patient care.',
                        'medium' => 'Good collaboration abilities with healthcare teams. You can work effectively with other professionals and contribute to team-based care. Continue developing your interdisciplinary communication and collaboration skills.',
                        'low' => 'Interdisciplinary collaboration development needed. Focus on building teamwork skills, improving communication with other healthcare professionals, and contributing to coordinated care.'
                    ],
                    'safety_awareness' => [
                        'high' => 'Exceptional safety awareness and practices. You consistently prioritize patient and staff safety, follow protocols diligently, and identify potential safety concerns proactively.',
                        'medium' => 'Good safety awareness with solid adherence to protocols. You maintain safety standards and follow established procedures. Continue developing your safety consciousness and proactive safety practices.',
                        'low' => 'Safety awareness development needed. Focus on understanding safety protocols, maintaining safety standards, and developing proactive safety practices.'
                    ]
                ]
            ]
        ]);

        // 4. Finance Industry Feedback Library
        FeedbackLibrary::create([
            'name' => 'Finance Industry Feedback',
            'feedback' => [
                'dimensions' => [
                    'financial_analysis' => [
                        'high' => 'Exceptional financial analysis skills demonstrated. Your ability to analyze complex financial data, identify trends, and provide insightful recommendations is outstanding. You show strong quantitative and analytical capabilities.',
                        'medium' => 'Good financial analysis abilities with solid analytical skills. You can analyze financial data effectively and provide reasonable insights. Continue developing your analytical capabilities and financial modeling skills.',
                        'low' => 'Financial analysis development needed. Focus on building analytical skills, understanding financial concepts, and developing data analysis capabilities.'
                    ],
                    'risk_management' => [
                        'high' => 'Excellent risk management skills and awareness. You identify potential risks effectively, develop appropriate mitigation strategies, and maintain strong risk controls. Your risk assessment and management capabilities are outstanding.',
                        'medium' => 'Good risk management abilities with solid risk awareness. You can identify and address risks appropriately. Continue developing your risk assessment skills and risk mitigation strategies.',
                        'low' => 'Risk management development needed. Focus on building risk awareness, understanding risk assessment processes, and developing risk mitigation skills.'
                    ],
                    'regulatory_compliance' => [
                        'high' => 'Outstanding regulatory compliance knowledge and practices. You demonstrate excellent understanding of financial regulations and maintain strong compliance practices. Your attention to regulatory requirements is exceptional.',
                        'medium' => 'Good compliance knowledge with solid regulatory awareness. You understand and follow relevant regulations appropriately. Continue developing your regulatory knowledge and compliance practices.',
                        'low' => 'Regulatory compliance development needed. Focus on understanding financial regulations, building compliance awareness, and developing regulatory knowledge.'
                    ],
                    'client_relationship' => [
                        'high' => 'Exceptional client relationship management skills. You build strong client relationships, understand client needs effectively, and provide excellent service. Your client communication and relationship building are outstanding.',
                        'medium' => 'Good client relationship abilities with solid service skills. You can build and maintain client relationships effectively. Continue developing your client communication and relationship management skills.',
                        'low' => 'Client relationship development needed. Focus on building client communication skills, understanding client needs, and developing relationship management capabilities.'
                    ]
                ]
            ]
        ]);

        // 5. Leadership Development Feedback Library
        FeedbackLibrary::create([
            'name' => 'Leadership Development Feedback',
            'feedback' => [
                'dimensions' => [
                    'strategic_thinking' => [
                        'high' => 'Exceptional strategic thinking capabilities. You demonstrate excellent ability to think long-term, consider multiple perspectives, and develop comprehensive strategies. Your strategic vision and planning skills are outstanding.',
                        'medium' => 'Good strategic thinking with solid planning abilities. You can think strategically and develop reasonable plans. Continue developing your strategic vision and long-term thinking capabilities.',
                        'low' => 'Strategic thinking development needed. Focus on building long-term thinking skills, understanding strategic concepts, and developing planning capabilities.'
                    ],
                    'decision_making' => [
                        'high' => 'Outstanding decision-making skills demonstrated. You make well-informed decisions, consider multiple factors, and take appropriate action. Your judgment and decision-making process are excellent.',
                        'medium' => 'Good decision-making abilities with solid judgment. You can make reasonable decisions and consider relevant factors. Continue developing your decision-making process and judgment skills.',
                        'low' => 'Decision-making development needed. Focus on building decision-making skills, improving judgment, and developing structured decision processes.'
                    ],
                    'influence_and_negotiation' => [
                        'high' => 'Excellent influence and negotiation skills. You can effectively persuade others, build consensus, and achieve positive outcomes through negotiation. Your ability to influence without authority is outstanding.',
                        'medium' => 'Good influence abilities with solid negotiation skills. You can persuade others and negotiate effectively in most situations. Continue developing your influence techniques and negotiation strategies.',
                        'low' => 'Influence and negotiation development needed. Focus on building persuasion skills, understanding negotiation techniques, and developing influence capabilities.'
                    ],
                    'team_development' => [
                        'high' => 'Exceptional team development skills. You effectively develop team capabilities, mentor team members, and create high-performing teams. Your ability to build and develop teams is outstanding.',
                        'medium' => 'Good team development abilities with solid mentoring skills. You can help develop team members and contribute to team growth. Continue developing your mentoring and team development skills.',
                        'low' => 'Team development skills needed. Focus on building mentoring abilities, understanding team dynamics, and developing team development capabilities.'
                    ]
                ]
            ]
        ]);

        $this->command->info('Feedback libraries seeded successfully!');
        $this->command->info('Created 5 feedback libraries:');
        $this->command->info('- General Assessment Feedback');
        $this->command->info('- Technology Industry Feedback');
        $this->command->info('- Healthcare Industry Feedback');
        $this->command->info('- Finance Industry Feedback');
        $this->command->info('- Leadership Development Feedback');
    }
}
