<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/PlantsRepository.php';
require_once __DIR__ . '/../repository/SpeciesRepository.php';

class MyPlantsController extends AppController {
    private PlantsRepository $plantsRepository;
    private SpeciesRepository $speciesRepository;

    // Create new repositories instances
    public function __construct()
    {
        $this->plantsRepository = new PlantsRepository();
        $this->speciesRepository = new SpeciesRepository();
    }

    // Show list of plants and load list of species
    public function index() {
        // Require user to be logged in
        $this->requireLogin();

        // Retrieve data about the currently logged-in user
        $user = $this->getUser();
        $userId = (int)$user['id'];

        // Fetch plants for this user
        $plants = $this->plantsRepository->getPlantsForUser($userId);

        // Fetch all species list
        $allSpecies = $this->speciesRepository->findAllSpecies();

        // Render view
        return $this->render("my-plants", [
            'user' => $user,
            'plants' => $plants,
            'allSpecies' => $allSpecies
        ]);
    }

    public function create()
    {
        // Require user to be logged in
        $this->requireLogin();

        // If the request is GET show My Plants page
        if ($this->isGet()) {
            return $this->redirect('/my-plants');
        }

        // Get user information
        $user = $this->getUser();
        $userId = (int)$user['id'];

        // Get plant information from form
        $speciesIdRaw = $_POST['species_id'] ?? null;
        $plantNameRaw = $_POST['plant_name'] ?? '';

        // Check if field is not empty
        if (empty($speciesIdRaw)) {
            return $this->redirect("/my-plants", [
                "messages" => ["Fields cannot be empty"]
            ]);
        }

        // Type casting
        $speciesId = (int) $speciesIdRaw;
        $plantNameInput = trim($plantNameRaw);

        // Fill plant name with default if user doesn't provide it
        if ($plantNameInput === '') {
            $speciesName = $this->speciesRepository->findSpeciesNameById($speciesId);

            $plantName = $speciesName;
        } else {
            $plantName = $plantNameInput;
        }

        // Insert the new planta in the database
        $this->plantsRepository->createPlant($userId, $speciesId, $plantName);

        // Redirect to My Plants page
        return $this->redirect('/my-plants');
    }

    public function update()
    {
        // Require user to be logged in
        $this->requireLogin();

        // Get user information
        $user = $this->getUser();
        $userId = (int)$user['id'];

        // GET: show update form
        if ($this->isGet()) {
            $plantIdRaw = $_GET['plant_id'] ?? null;
            if (empty($plantIdRaw)) {
                return $this->redirect('/my-plants');
            }

            $plantId = (int)$plantIdRaw;
            $plant = $this->plantsRepository->getPlantForUser($plantId, $userId);

            if (!$plant) {
                return $this->redirect('/my-plants');
            }

            $allSpecies = $this->speciesRepository->findAll();

            // Render view
            return $this->render('update-plant', [
                'user'       => $user,
                'plant'      => $plant,
                'allSpecies' => $allSpecies
            ]);
        }

        // POST: update plant
        $plantIdRaw   = $_POST['plant_id'] ?? null;
        $speciesIdRaw = $_POST['species_id'] ?? null;
        $plantNameRaw = $_POST['plant_name'] ?? '';

        if (empty($plantIdRaw) || empty($speciesIdRaw)) {
            return $this->redirect('/my-plants', [
                'messages' => ['Fields cannot be empty']
            ]);
        }

        $plantId   = (int)$plantIdRaw;
        $speciesId = (int)$speciesIdRaw;
        $plantNameInput = trim($plantNameRaw);
        // same default logic as in create()
        if ($plantNameInput === '') {
            $speciesName = $this->speciesRepository->findSpeciesNameById($speciesId);
            $plantName   = $speciesName;
        } else {
            $plantName = $plantNameInput;
        }

        $this->plantsRepository->updatePlant(
            $plantId,
            $userId,
            $speciesId,
            $plantName,
        );

        return $this->redirect('/my-plants');
    }

    public function delete()
    {
        // Require user to be logged in
        $this->requireLogin();

        // If the request is POST show My Plants page
        if ($this->isGet()) {
            return $this->redirect('/my-plants');
        }

        // Get user information
        $user = $this->getUser();
        $userId = (int)$user['id'];

        // Get plant information
        $plantIdRaw = $_POST['plant_id'] ?? null;

        // If it doesn't exist show My Plants page
        if (empty($plantIdRaw)) {
            return $this->redirect('/my-plants');
        }

        // Type casting
        $plantId = (int)$plantIdRaw;

        // Delete plant from database
        $this->plantsRepository->deletePlant($plantId, $userId);

        // Redirect to My Plants page
        return $this->redirect('/my-plants');
    }
}
