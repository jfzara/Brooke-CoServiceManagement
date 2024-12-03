// src/app/core/constants/intervention.constants.ts

export const STATUS_TYPES = {
    DRAFT: 'Brouillon',
    PLANNED: 'Planifiée',
    ASSIGNED: 'Assignée',
    PENDING_VALIDATION: 'En attente de validation',
    PENDING: 'En attente',
    IN_PROGRESS: 'En cours',
    COMPLETED: 'Terminé',
    CANCELLED: 'Annulé'
  } as const;
  
  export const PRIORITE_TYPES = {
    URGENT: 'Urgent',
    HIGH: 'Haute',
    NORMAL: 'Normale',
    LOW: 'Basse'
  } as const;
  
  export const TYPE_INTERVENTIONS = {
    INSTALLATION_FIBRE: 'Installation Fibre',
    DEPANNAGE_INTERNET: 'Dépannage Internet',
    CONFIGURATION_WIFI: 'Configuration WiFi',
    INSTALLATION_TV: 'Installation TV',
    MAINTENANCE_BOX: 'Maintenance Box'
  } as const;
  
  export const getStatusColor = (status: string): string => {
    switch (status) {
      case STATUS_TYPES.DRAFT:
        return '#B0BEC5';
      case STATUS_TYPES.PLANNED:
        return '#90CAF9';
      case STATUS_TYPES.ASSIGNED:
        return '#81D4FA';
      case STATUS_TYPES.PENDING_VALIDATION:
        return '#FFE082';
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
  
  export const getPrioriteColor = (priorite: string): string => {
    switch (priorite) {
      case PRIORITE_TYPES.URGENT:
        return '#f44336';
      case PRIORITE_TYPES.HIGH:
        return '#ff9800';
      case PRIORITE_TYPES.NORMAL:
        return '#4caf50';
      case PRIORITE_TYPES.LOW:
        return '#2196f3';
      default:
        return '#9e9e9e';
    }
  };