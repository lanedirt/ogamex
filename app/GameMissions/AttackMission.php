<?php

namespace OGame\GameMissions;

use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\EspionageReport;
use OGame\Models\FleetMission;
use OGame\Services\PlanetService;
use Throwable;

class AttackMission extends GameMission
{
    protected static string $name = 'Attack';
    protected static int $typeId = 1;
    protected static bool $hasReturnMission = true;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, ?PlanetService $targetPlanet, UnitCollection $units): MissionPossibleStatus
    {
        // If planet does not exist, the mission is not possible.
        if ($targetPlanet === null) {
            return new MissionPossibleStatus(false);
        }

        // If planet belongs to current player, the mission is not possible.
        if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return new MissionPossibleStatus(false);
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    protected function processArrival(FleetMission $mission): void
    {
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to);
        $origin_planet = $this->planetServiceFactory->make($mission->planet_id_from);

        // Trigger target planet update to make sure the espionage report is accurate.
        $target_planet->update();

        // TODO: add battle logic here. For now, we just mark the mission as processed.
        /*$reportId = $this->createEspionageReport($target_planet);

        // Send a message to the player with a reference to the espionage report.
        $this->messageService->sendEspionageReportMessageToPlayer(
            $origin_planet->getPlayer(),
            $reportId,
        );*/

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Check if the mission has any ships left. If yes, start a return mission to send them back.
        if ($this->fleetMissionService->getFleetUnitCount($mission) > 0) {
            // Create and start the return mission.
            $this->startReturn($mission);
        }
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to);

        // Attack return trip: add back the units to the source planet. Then we're done.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->sum() > 0) {
            $target_planet->addResources($return_resources);
        }

        // TODO: send return message to player that fleet has returned.

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * Creates an espionage report for the target planet.
     *
     * @param PlanetService $planet
     * @return int
     */
    private function createEspionageReport(PlanetService $planet): int
    {
        // TODO: make sure the target planet is updated with the latest resources before creating the report
        // to ensure the report is accurate at the current point in time.
        // TODO: add planet update call here and add a test to cover this.

        // Create new espionage report record.
        $report = new EspionageReport();
        $report->planet_galaxy = $planet->getPlanetCoordinates()->galaxy;
        $report->planet_system = $planet->getPlanetCoordinates()->system;
        $report->planet_position = $planet->getPlanetCoordinates()->position;

        $report->planet_user_id = $planet->getPlayer()->getId();

        $report->player_info = [
            'player_id' => (string)$planet->getPlayer()->getId(),
            'player_name' => $planet->getPlayer()->getUsername(),
        ];

        // Resources
        $report->resources = [
            'metal' => (int)$planet->metal()->get(),
            'crystal' => (int)$planet->crystal()->get(),
            'deuterium' => (int)$planet->deuterium()->get(),
            'energy' => (int)$planet->energy()->get()
        ];

        // TODO: implement logic which determines what to include in the espionage report based on
        // the player's espionage technology level. For example, the player can see more details about the
        // target planet if the espionage technology level is higher.

        // Fleets
        $report->ships = $planet->getShipsArray();

        // Defense
        $report->defense = $planet->getDefenseArray();

        // Buildings
        $report->buildings = $planet->getBuildingArray();

        // Research
        $report->research = $planet->getPlayer()->getResearchArray();

        $report->save();

        return $report->id;
    }
}