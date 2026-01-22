export interface AppErrorT extends Error {
  statusCode: number;
  messageDetail: string | object;
}

export interface ResponseT {
  success: boolean;
  error?: string;
  message?: string | object;
  data?: object;
}

export interface UserCreate {
  name: string;
  username: string;
  email: string;
  password: string;
  confirm_password: string;
}

export interface UserLogin {
  email: string;
  password: string;
}

export interface UserUpdate {
  name?: string;
  password?: string;
  confirm_password?: string;
}

export interface ProjectCreate {
  name: string;
  description?: string;
  status: string;
  start_date?: string;
  end_date?: string;
}

export interface ProjectParams {
  id: string;
}

export interface ProjectUpdate {
  name?: string;
  description?: string;
  status?: string;
  start_date?: string;
  end_date?: string;
}
