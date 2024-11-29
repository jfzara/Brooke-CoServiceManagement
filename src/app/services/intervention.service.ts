import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

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
  Nom?: string;
  Prenom?: string;
  DateIntervention?: string;
  HeureDebut?: string;
  HeureFin?: string;
  InterventionCommentaires?: string;
  PlanningCommentaires?: string;
}

export const STATUS_TYPES = {
  PENDING: 'En attente',
  IN_PROGRESS: 'En cours',
  COMPLETED: 'Terminé',
  CANCELLED: 'Annulé'
} as const;

export const getStatusColor = (status: string): string => {
  switch (status) {
    case STATUS_TYPES.PENDING:
      return '#FFD700'; // Gold
    case STATUS_TYPES.IN_PROGRESS:
      return '#4CAF50'; // Green
    case STATUS_TYPES.COMPLETED:
      return '#2196F3'; // Blue
    case STATUS_TYPES.CANCELLED:
      return '#f44336'; // Red
    default:
      return '#9E9E9E'; // Grey
  }
};

@Injectable({
  providedIn: 'root'
})
export class InterventionService {
  private apiUrl = 'http://localhost:8000/api.php';

  constructor(private http: HttpClient) { }

  getTechnicienInterventions(technicienId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}?action=get_technicien_interventions&technicienId=${technicienId}`);
  }

  getPlanningByTechnicienWithMoreInfos(technicienId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}?action=get_technicien_interventions&technicienId=${technicienId}`);
  }

  updateStatusIntervention(data: { InterventionID: number, StatutIntervention: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}?action=update_intervention_status`, data);
  }

  getInterventionById(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}?action=get_intervention&id=${id}`);
  }

  updateIntervention(id: number, data: Partial<Intervention>): Observable<any> {
    return this.http.post(`${this.apiUrl}?action=update_intervention`, { id, ...data });
  }

  deleteIntervention(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}?action=delete_intervention&id=${id}`);
  }
}