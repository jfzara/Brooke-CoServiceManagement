export interface ApiResponse<T> {
    status: 'success' | 'error';
    data?: T;
    message?: string;
    result?: {
        utilisateur?: {
            UtilisateurID: number;
            Email: string;
            Nom: string;
            Prenom: string;
            Type: string;
        };
        token?: string;
    };
}

export interface ApiError {
    status: 'error';
    message: string;
    code?: number;
}

export interface PaginatedResponse<T> extends ApiResponse<T> {
    pagination?: {
        currentPage: number;
        totalPages: number;
        totalItems: number;
        itemsPerPage: number;
    };
}

export interface ApiLoginRequest {
    email: string;
    motDePasse: string;
}

export interface ApiLoginResponse extends ApiResponse<void> {
    token?: string;
    user?: {
        UtilisateurID: number;
        Email: string;
        Nom: string;
        Prenom: string;
        Type: string;
    };
}

export interface ApiRegisterRequest {
    email: string;
    motDePasse: string;
    nom: string;
    prenom: string;
    type: 'client' | 'technicien' | 'prepose';
    adresse?: string;
    telephone?: string;
}

export type ApiMethod = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';

export interface ApiRequestConfig {
    method: ApiMethod;
    endpoint: string;
    params?: Record<string, string | number>;
    data?: any;
    headers?: Record<string, string>;
}