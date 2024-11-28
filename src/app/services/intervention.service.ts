import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environment';

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
  private apiUrl = `${environment.apiUrl}/interventions`;

  constructor(private http: HttpClient) { }

  getPlanningByTechnicienWithMoreInfos(technicienId: number): Observable<Intervention[]> {
    return this.http.get<Intervention[]>(`${this.apiUrl}/technicien/${technicienId}`);
  }

  updateStatusIntervention(data: { InterventionID: number, StatutIntervention: string }): Observable<any> {
    return this.http.put(`${this.apiUrl}/${data.InterventionID}/status`, data);
  }

  getInterventionById(id: number): Observable<Intervention> {
    return this.http.get<Intervention>(`${this.apiUrl}/${id}`);
  }

  updateIntervention(id: number, data: Partial<Intervention>): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, data);
  }

  deleteIntervention(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}