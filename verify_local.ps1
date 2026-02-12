$baseUrl = "http://127.0.0.1:8000/api"

# Use curl.exe directly to be strictly stateless and avoid Invoke-RestMethod's hidden cookie handling
function Invoke-Curl {
    param($Path, $Method, $Body, $Token)
    $args = @("-s", "-X", $Method, "$baseUrl$Path", "-H", "Accept: application/json", "-H", "Content-Type: application/json")
    if ($Token) {
        $args += "-H", "Authorization: Bearer $Token"
    }
    if ($Body) {
        $args += "-d", $Body
    }
    $res = & "curl.exe" @args
    return $res | ConvertFrom-Json
}

# 1. Login
Write-Host "--- Logging in ---"
$loginBody = '{"email":"petugas@antropometri.go.id", "password":"Petugas@123"}'
$loginRes = Invoke-Curl -Path "/auth/login" -Method "POST" -Body $loginBody
if (-not $loginRes.success) { 
    Write-Error "Login Failed"
    exit
}
$token = $loginRes.data.token
Write-Host "Logged in. Token: $($token.Substring(0,10))..."

# 2. Create/Use Subject
Write-Host "`n--- Creating Subject ---"
$subjectBody = '{"name":"Local Test Salman", "date_of_birth":"1990-01-01", "gender":"L", "nik":"11223344"}'
$subjectRes = Invoke-Curl -Path "/subjects" -Method "POST" -Body $subjectBody -Token $token

if (-not $subjectRes.success) {
    if ($subjectRes.message -like "*sudah ada*") {
        $subjectId = $subjectRes.data.existing_subject.id
        Write-Host "Using existing subject ID: $subjectId"
    } else {
        Write-Error "Subject error: $($subjectRes.message)"
        exit
    }
} else {
    $subjectId = $subjectRes.data.subject.id
    Write-Host "Created subject ID: $subjectId"
}

# 3. Add Measurement
Write-Host "`n--- Adding Measurement ---"
$dateStr = (Get-Date).ToString("yyyy-MM-dd")
$measureBody = "{`"measurement_date`":`"$dateStr`", `"weight`":80.0, `"height`":175.0, `"waist_circumference`":105.0}"
$measureRes = Invoke-Curl -Path "/subjects/$subjectId/measurements" -Method "POST" -Body $measureBody -Token $token

if (-not $measureRes.success) {
    Write-Error "Measurement failed: $($measureRes.message)"
    $measureRes | ConvertTo-Json
    exit
}
Write-Host "Measurement Added."

# 4. Verify Detail
Write-Host "`n--- Verifying Results ---"
$measureId = $measureRes.data.measurement.id
$detailRes = Invoke-Curl -Path "/measurements/$measureId" -Method "GET" -Token $token

$res = $detailRes.data.measurement.result
Write-Host "BMI: $($res.bmi) (Status: $($res.bmi_status))"
Write-Host "Obesity: $($res.central_obesity_status)"
Write-Host "Rec: $($detailRes.data.measurement.recommendation)"
