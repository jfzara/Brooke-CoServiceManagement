import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environment';

@Injectable({
  providedIn: 'root'
})
export class InterventionService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getPlanningByTechnicienWithMoreInfos(technicienId: number): Observable<any> {
    const url = `${this.apiUrl}?action=planning&TechnicienID=${technicienId}`;
    return this.http.get<any[]>(url);
  }

  updateStatusIntervention(data: any): Observable<any> {
    const url = `${this.apiUrl}?action=updateStatusIntervention`;
    return this.http.put(url, data);
  }

  getInterventionById(id: number): Observable<any> {
    const url = `${this.apiUrl}?action=getIntervention&id=${id}`;
    return this.http.get(url);
  }

  updateIntervention(id: number, data: any): Observable<any> {
    const url = `${this.apiUrl}?action=updateIntervention&id=${id}`;
    return this.http.put(url, data);
  }

  deleteIntervention(id: number): Observable<any> {
    const url = `${this.apiUrl}?action=deleteIntervention&id=${id}`;
    return this.http.delete(url);
  }

  getTechniciens(): Observable<any[]> {
    const url = `${this.apiUrl}?action=getTechniciens`;
    return this.http.get<any[]>(url);
  }
}