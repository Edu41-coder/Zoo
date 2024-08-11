// Importer Axios
import axios from 'axios';

// Fonction pour mettre à jour le service
export const updateService = async (id, nom, description) => {
    try {
        const response = await axios.post('modif_service.php', {
            id: id,
            nom: nom,
            description: description
        });
        return response.data;
    } catch (error) {
        throw error;
    }
};

// Fonction pour ajouter un habitat
export const addHabitat = async (nom, description, imageFile) => {
    const formData = new FormData();
    formData.append('nom', nom);
    formData.append('description', description);
    formData.append('image_id', imageFile);

    try {
        const response = await axios.post('ajouter_habitat.php', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        return response.data;
    } catch (error) {
        throw error;
    }
};