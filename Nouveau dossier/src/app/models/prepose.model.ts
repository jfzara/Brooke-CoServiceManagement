import { User } from './user.model';

export interface Prepose {
    PreposeID: number;
    UtilisateurID: number;
    utilisateur?: User;
}

export interface PreposeComplet extends Prepose {
    Nom: string;
    Prenom: string;
    Email: string;
}