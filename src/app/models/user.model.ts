export interface User {
    id: number;
    email: string;
    prenom: string;
    nom: string;
    type: string;
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