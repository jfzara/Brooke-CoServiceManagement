export interface Intervention {
    InterventionID?: number;
    TechnicienID: number;
    ClientID: number;
    TypeIntervention: string;
    Description: string;
    DebutIntervention: string;
    FinIntervention: string;
    StatutIntervention: string;
    Commentaires?: string;
    client?: Client;
  }
  
  export interface Client {
    Nom: string;
    Prenom: string;
    Adresse: string;
    Telephone: string;
  }
  
  export const STATUS_TYPES = {
    PENDING: 'En attente',
    IN_PROGRESS: 'En cours',
    COMPLETED: 'Terminé',
    CANCELLED: 'Annulé'
  } as const;
  
  export type StatusType = typeof STATUS_TYPES[keyof typeof STATUS_TYPES];
  
  export const getStatusColor = (status: string): string => {
    switch (status) {
      case STATUS_TYPES.PENDING:
        return '#FFD700';
      case STATUS_TYPES.IN_PROGRESS:
        return '#4CAF50';
      case STATUS_TYPES.COMPLETED:
        return '#2196F3';
      case STATUS_TYPES.CANCELLED:
        return '#f44336';
      default:
        return '#9E9E9E';
    }
  };