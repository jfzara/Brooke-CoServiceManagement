export interface User {
  id?: number;
  UtilisateurID?: number;
  Email: string;
  Nom?: string;
  Prenom?: string;
  Type?: string;
  technicienId?: number;
}

export interface AuthResponse {
  status: string;
  result?: {
    utilisateur: User;
    token?: string;
  };
  message?: string;
}

export interface UserProfile {
  fullName: string;
  email: string;
  type: string;
  technicienId?: number;
}