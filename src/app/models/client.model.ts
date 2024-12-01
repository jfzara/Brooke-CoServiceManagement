import { User } from './user.model';

export interface Client {
    ClientID: number;
    UtilisateurID: number;
    Adresse: string;
    Telephone: string;
    Demandes?: string;
    utilisateur?: User;
}

export interface ClientComplet extends Client {
    Nom: string;
    Prenom: string;
    Email: string;
}

export interface ClientCreationDTO {
    Nom: string;
    Prenom: string;
    Email: string;
    MotDePasse: string;
    Adresse: string;
    Telephone: string;
    Demandes?: string;
}