<#
.SYNOPSIS
  Login to patho_api and create a doctor record using the same PHP session cookie.

.DESCRIPTION
  This script performs a POST to login.php to obtain the PHP session, then POSTs to
  doctor.php?action=save using the same session so the API recognizes an authenticated user.

.PARAMETER BaseUrl
  Base URL for the patho_api folder (default: https://hospital.codeapka.com/umakant/patho_api)

.PARAMETER Username
  Username to login with. If not provided the script will prompt.

.PARAMETER Password
  Password to login with. If not provided the script will prompt securely.

.EXAMPLE
  .\save_doctor.ps1 -Username admin -Name 'Dr Demo' -Email 'd@e.com'

  Prompts for password, logs in, then creates the doctor and prints the JSON response.
#>

param(
    [string]$BaseUrl = 'https://hospital.codeapka.com/umakant/patho_api',
    [string]$Username,
    [System.Security.SecureString]$Password,
    [string]$Name = 'Dr Test',
    [string]$Qualification = '',
    [string]$Specialization = '',
    [string]$Hospital = '',
    [string]$ContactNo = '',
    [string]$Phone = '',
    [string]$Email = '',
    [string]$Address = '',
    [string]$RegistrationNo = '',
    [double]$Percent = 1
)

function Convert-SecureStringToPlainText {
    param([System.Security.SecureString]$secure)
    if (-not $secure) { return '' }
    $bstr = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure)
    try { [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($bstr) }
    finally { [System.Runtime.InteropServices.Marshal]::ZeroFreeBSTR($bstr) }
}

if (-not $Username) { $Username = Read-Host 'Username' }
if (-not $Password) { $Password = Read-Host 'Password' -AsSecureString }

$plainPassword = Convert-SecureStringToPlainText -secure $Password

Write-Host "Logging in to $BaseUrl/login.php as $Username..."

try {
    $loginBody = @{ username = $Username; password = $plainPassword }
    # -SessionVariable stores cookies in $sess for reuse
    $loginResp = Invoke-RestMethod -Uri (Join-Path $BaseUrl 'login.php') -Method Post -Body $loginBody -SessionVariable sess -ErrorAction Stop
} catch {
    Write-Error "Login request failed: $_"
    exit 1
}

if (-not $loginResp.success) {
    Write-Error "Login failed: $($loginResp.message)"
    exit 2
}

Write-Host "Login successful (user id: $($loginResp.user.id)). Creating doctor..."

$saveBody = @{
    name = $Name
    qualification = $Qualification
    specialization = $Specialization
    hospital = $Hospital
    contact_no = $ContactNo
    phone = $Phone
    email = $Email
    address = $Address
    registration_no = $RegistrationNo
    percent = $Percent
}

try {
    $saveResp = Invoke-RestMethod -Uri (Join-Path $BaseUrl 'doctor.php?action=save') -Method Post -Body $saveBody -WebSession $sess -ErrorAction Stop
    Write-Host "Server response:`n" (ConvertTo-Json $saveResp -Depth 5)
} catch {
    Write-Error "Save request failed: $_"
    exit 3
}

exit 0
