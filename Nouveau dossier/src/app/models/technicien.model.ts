import { User } from './user.model';

export interface Technicien {
    TechnicienID: number;
    UtilisateurID: number;
    Nom: string;
    Prenom: string;
    Email: string;
    utilisateur?: User;
}

export interface TechnicienComplet extends Technicien {
    Nom: string;
    Prenom: string;
    Email: string;
}

export interface DisponibiliteHebdomadaire {
    DisponibiliteID: number;
    TechnicienID: number;
    NumeroSemaine: number;
    JourDisponible: Date;
    DebutDisponibilite: Date;
    FinDisponibilite: Date;
}

export interface TechnicienDisponibilite {
    TechnicienID: number;
    Nom: string;
    Prenom: string;
    disponible: boolean;
    disponibilites?: {
        date: string;
        heureDebut: string;
        heureFin: string;
    }[];
}

export interface TechnicienAvecCharge {
    technicien: TechnicienComplet;
    nombreInterventions: number;
    prochaineDisponibilite?: string;
}