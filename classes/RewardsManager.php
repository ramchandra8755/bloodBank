<?php
class RewardsManager {
    private $db;
    private $levelThresholds = [
        'Bronze' => 0,
        'Silver' => 500,
        'Gold' => 1000,
        'Platinum' => 2000,
        'Diamond' => 5000
    ];
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    private function addPointsHistory($donorId, $points, $description, $type, $donationDate = null) {
        // Check if this entry already exists for the same day
        $query = "SELECT id FROM points_history 
                 WHERE donor_id = ? 
                 AND points = ? 
                 AND type = ? 
                 AND DATE(created_at) = DATE(?)";
        
        $stmt = $this->db->prepare($query);
        $date = $donationDate ?? date('Y-m-d');
        $stmt->execute([$donorId, $points, $type, $date]);
        
        // If entry doesn't exist, add it
        if (!$stmt->fetch()) {
            $query = "INSERT INTO points_history (donor_id, points, description, type, created_at) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$donorId, $points, $description, $type, $date]);
        }
        
        return false;
    }
    
    public function updateDonorRewards($donorId) {
        $inTransaction = $this->db->inTransaction();
        
        if (!$inTransaction) {
            $this->db->beginTransaction();
        }
        
        try {
            // Get latest donation that hasn't been recorded in points history
            $query = "SELECT dh.* 
                     FROM donation_history dh
                     LEFT JOIN points_history ph 
                        ON ph.donor_id = dh.donor_id 
                        AND DATE(ph.created_at) = DATE(dh.donation_date)
                        AND ph.type = 'donation'
                     WHERE dh.donor_id = ? 
                     AND ph.id IS NULL
                     ORDER BY dh.donation_date DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$donorId]);
            $newDonations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get all donor's donations for total calculation
            $query = "SELECT 
                        COUNT(*) as total_donations,
                        SUM(units) as total_units,
                        MAX(donation_date) as last_donation
                     FROM donation_history 
                     WHERE donor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$donorId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Let's add debug logging
            error_log("Donor Stats: " . print_r($stats, true));
            
            // Calculate total points with detailed logging
            $donationPoints = $stats['total_donations'] * 100;
            error_log("Base donation points: " . $donationPoints);
            
            $unitPoints = $stats['total_units'] * 50;
            error_log("Unit bonus points: " . $unitPoints);
            
            $streakPoints = $this->calculateStreakPoints($donorId);
            error_log("Streak points: " . $streakPoints);
            
            $totalPoints = $donationPoints + $unitPoints + $streakPoints;
            error_log("Total points: " . $totalPoints);
            
            // Record points for new donations
            foreach ($newDonations as $donation) {
                // Base donation points
                $this->addPointsHistory(
                    $donorId,
                    100,
                    "Blood donation completed",
                    "donation",
                    $donation['donation_date']
                );
                
                // Unit bonus points
                if ($donation['units'] > 0) {
                    $this->addPointsHistory(
                        $donorId,
                        $donation['units'] * 50,
                        "Bonus points for units donated",
                        "donation",
                        $donation['donation_date']
                    );
                }
            }
            
            // Record streak points if any (only once per streak)
            if ($streakPoints > 0) {
                $this->addPointsHistory(
                    $donorId,
                    $streakPoints,
                    "Regular donor streak bonus",
                    "streak"
                );
            }
            
            // Calculate badges
            $badges = $this->calculateBadges($donorId, $stats);
            
            // Determine level
            $level = $this->calculateLevel($totalPoints);
            
            // Update rewards record
            $query = "INSERT INTO donor_rewards 
                     (donor_id, points, level, total_donations, badges, last_updated) 
                     VALUES (?, ?, ?, ?, ?, NOW()) 
                     ON DUPLICATE KEY UPDATE 
                     points = VALUES(points),
                     level = VALUES(level),
                     total_donations = VALUES(total_donations),
                     badges = VALUES(badges),
                     last_updated = NOW()";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $donorId,
                $totalPoints,
                $level,
                $stats['total_donations'],
                json_encode($badges)
            ]);
            
            if (!$inTransaction) {
                $this->db->commit();
            }
            
            return [
                'points' => $totalPoints,
                'level' => $level,
                'total_donations' => $stats['total_donations'],
                'badges' => $badges,
                'next_level' => $this->getNextLevelInfo($totalPoints, $level),
                'streak_points' => $streakPoints
            ];
            
        } catch (Exception $e) {
            if (!$inTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw new Exception("Error updating rewards: " . $e->getMessage());
        }
    }
    
    private function calculateStreakPoints($donorId) {
        $query = "SELECT donation_date 
                 FROM donation_history 
                 WHERE donor_id = ? 
                 AND donation_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                 ORDER BY donation_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$donorId]);
        $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $streakPoints = 0;
        $consecutiveMonths = 0;
        
        for ($i = 0; $i < count($donations) - 1; $i++) {
            $date1 = new DateTime($donations[$i]['donation_date']);
            $date2 = new DateTime($donations[$i + 1]['donation_date']);
            $interval = $date1->diff($date2);
            
            if ($interval->m >= 3 && $interval->m <= 4) {
                $consecutiveMonths++;
                if ($consecutiveMonths >= 2) {
                    $streakPoints += 200;
                }
            } else {
                $consecutiveMonths = 0;
            }
        }
        
        return $streakPoints;
    }
    
    private function calculateLevel($points) {
        foreach (array_reverse($this->levelThresholds) as $level => $threshold) {
            if ($points >= $threshold) {
                return $level;
            }
        }
        return 'Bronze';
    }
    
    private function getNextLevelInfo($points, $currentLevel) {
        $levels = array_keys($this->levelThresholds);
        $currentIndex = array_search($currentLevel, $levels);
        
        if ($currentIndex < count($levels) - 1) {
            $nextLevel = $levels[$currentIndex + 1];
            $pointsNeeded = $this->levelThresholds[$nextLevel] - $points;
            return [
                'next_level' => $nextLevel,
                'points_needed' => $pointsNeeded
            ];
        }
        return null;
    }
    
    private function calculateBadges($donorId, $stats) {
        $badges = [];
        
        // Donation count badges
        $donationMilestones = [1, 5, 10, 25, 50, 100];
        foreach ($donationMilestones as $milestone) {
            if ($stats['total_donations'] >= $milestone) {
                $badges[] = [
                    'type' => 'achievement',
                    'name' => $milestone . ' Donations',
                    'description' => 'Completed ' . $milestone . ' blood donations',
                    'icon' => 'donation-' . $milestone
                ];
            }
        }
        
        // Streak badges
        if ($this->calculateStreakPoints($donorId) > 0) {
            $badges[] = [
                'type' => 'achievement',
                'name' => 'Regular Donor',
                'description' => 'Maintained regular donation schedule',
                'icon' => 'streak'
            ];
        }
        
        return $badges;
    }
    
    // Add this helper function to check donation details
    public function getDonationDetails($donorId) {
        $query = "SELECT 
                    donation_date,
                    units,
                    (units * 50 + 100) as points_earned
                  FROM donation_history 
                  WHERE donor_id = ?
                  ORDER BY donation_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$donorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 