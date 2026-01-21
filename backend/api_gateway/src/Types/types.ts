export interface AppErrorT extends Error {
  statusCode: number;
}

export interface ResponseT {
  success: boolean;
  error?: string;
  message?: string;
  data?: {};
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
