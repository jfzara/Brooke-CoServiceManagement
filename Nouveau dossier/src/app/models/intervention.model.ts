// src/app/models/intervention.model.ts

import {
  STATUS_TYPES,
  PRIORITE_TYPES,
  TYPE_INTERVENTIONS,
  getStatusColor,
  getPrioriteColor
} from '../core/constants/intervention.constants';

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
  DateCreation?: string;
  DateAssignation?: string;
  Priorite?: PrioriteType;
  isNew?: boolean;
}

export interface Client {
  Nom: string;
  Prenom: string;
  Adresse: string;
  Telephone: string;
}

export type StatusType = typeof STATUS_TYPES[keyof typeof STATUS_TYPES];
export type PrioriteType = typeof PRIORITE_TYPES[keyof typeof PRIORITE_TYPES];
export type TypeIntervention = typeof TYPE_INTERVENTIONS[keyof typeof TYPE_INTERVENTIONS];

export {
  STATUS_TYPES,
  PRIORITE_TYPES,
  TYPE_INTERVENTIONS,
  getStatusColor,
  getPrioriteColor
};