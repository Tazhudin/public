<?php

Bitrix\Main\Loader::registerAutoloadClasses(
    'recipes.05',
    [
        'Recipes_05\\Recipe\\RecipeListHelper' => 'lib/Recipe/RecipeListHelper.php',
        'Recipes_05\\Recipe\\RecipeEditHelper' => 'lib/Recipe/RecipeEditHelper.php',
        'Recipes_05\\Recipe\\RecipeAdminInterface' => 'lib/Recipe/RecipeAdminInterface.php',
        'Recipes_05\\Ingredients\\IngredientsListHelper' => 'lib/Ingredients/IngredientsListHelper.php',
        'Recipes_05\\Ingredients\\IngredientsEditHelper' => 'lib/Ingredients/IngredientsEditHelper.php',
        'Recipes_05\\Ingredients\\IngredientsAdminInterface' => 'lib/Ingredients/IngredientsAdminInterface.php',
        'Recipes_05\\CookingSteps\\CookingStepsListHelper' => 'lib/CookingSteps/CookingStepsListHelper.php',
        'Recipes_05\\CookingSteps\\CookingStepsEditHelper' => 'lib/CookingSteps/CookingStepsEditHelper.php',
        'Recipes_05\\CookingSteps\\CookingStepsAdminInterface' => 'lib/CookingSteps/CookingStepsAdminInterface.php',
        'Recipes_05\\Permission' => 'lib/Permission.php',
        'Recipes_05\\PermissionTrait' => 'lib/PermissionTrait.php',
    ]
);
