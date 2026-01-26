class AppError extends Error {
  public readonly statusCode: number;
  public readonly messageDetail: string | object;

  constructor(code: number, message: string | object) {
    super("App error, check in message details.");

    this.statusCode = code;
    this.messageDetail = message;

    Error.captureStackTrace(this, this.constructor);
  }
}

export default AppError;
