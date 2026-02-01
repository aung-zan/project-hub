export interface Response {
  success: boolean;
  error?: string;
  message?: string | Record<string, string[]>;
  data?: object;
}

export interface ErrorResponse {
  success: false;
  error?: string;
  message: string | Record<string, string[]>;
}

export interface ConnectionConfig {
  path: string;
  method: "post" | "put" | "get";
  data?: UserCreate | UserAuth;
  token: boolean;
}

export interface UserCreate {
  name: string;
  username: string;
  email: string;
  password: string;
  confirm_password: string;
}

export type UserAuth = Pick<UserCreate, "email" | "password">;
