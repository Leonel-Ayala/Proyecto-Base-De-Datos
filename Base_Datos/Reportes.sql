-- Consulta De citas Veterinario


SELECT 
    V.Nombre || ' ' || V.Apellido1 || ' ' || V.Apellido2 AS Veterinario,
    COUNT(C.ID_Cita) AS Total_Citas
FROM 
    LAROATLB_Cita C
JOIN 
    LAROATLB_Veterinario V ON C.ID_Veterinario = V.ID_Veterinario
WHERE 
    C.Fecha BETWEEN TO_DATE('2024-11-01', 'YYYY-MM-DD') AND TO_DATE('2024-11-30', 'YYYY-MM-DD')
GROUP BY 
    V.Nombre, V.Apellido1, V.Apellido2
ORDER BY 
    Total_Citas DESC;





-- Consulta Ficha Clinica Mascota



