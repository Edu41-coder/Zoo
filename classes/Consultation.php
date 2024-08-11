<?php
require_once(__DIR__ . '/Database_Mongo.php'); // Inclure la classe Database_Mongo

class Consultation
{
    private $collection; // Collection MongoDB pour les consultations par animal

    // Le constructeur accepte un argument pour la connexion à la base de données MongoDB
    public function __construct($bdd)
    {
        // Assurez-vous que $bdd est une instance de MongoDB\Database
        if ($bdd instanceof MongoDB\Database) {
            $this->collection = $bdd->consultationParAnimal; // Initialiser la collection
        } else {
            throw new Exception('Invalid MongoDB connection'); // Lever une exception si la connexion est invalide
        }
    }

    // Méthode pour mettre à jour une consultation
    public function updateConsultation(int $animal_id, string $prenom): void
    {
        // Rechercher une consultation existante pour l'animal
        $result = $this->collection->findOne(['animal_id' => $animal_id]);

        if ($result === null) {
            // Insérer une nouvelle consultation si aucune n'existe
            $this->collection->insertOne(['animal_id' => $animal_id, 'prenom' => $prenom, 'nbClique' => 1]);
        } else {
            // Mettre à jour la consultation existante
            $this->collection->updateOne(
                ['animal_id' => $animal_id],
                ['$inc' => ['nbClique' => 1], '$set' => ['prenom' => $prenom]]
            );
        }
    }

    // Méthode pour obtenir les consultations triées par nombre de clics
    public function getConsultations(): array
    {
        // Pipeline d'agrégation pour trier et projeter les champs nécessaires
        $pipeline = [
            [
                '$sort' => ['nbClique' => -1] // Trier par nbClique en ordre décroissant
            ],
            [
                '$project' => [
                    'animal_id' => 1,
                    'nbClique' => 1,
                    'prenom' => 1
                ]
            ]
        ];

        // Exécuter l'agrégation et retourner les résultats sous forme de tableau
        $result = $this->collection->aggregate($pipeline);
        return iterator_to_array($result);
    }
}
?>